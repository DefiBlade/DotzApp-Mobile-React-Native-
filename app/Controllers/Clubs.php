<?php namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ClubsModel;
use App\Models\ClubUsersModel;
use App\Models\AppUserModel;
use App\Models\AppUserRelsModel;
use App\Models\ClubMessagesModel;
use App\Models\ProfilesModel;
use App\Models\Profile_relsModel;
use App\Models\ClubInvitationsModel;
use \App\Libraries\Oauth;
use \OAuth2\Request;
use App\Models\NotificationModel;

class Clubs extends BaseController
{
    public function index()
    {
		$modelClubs = new ClubsModel;
		$data['page'] = 'clubs';
		$data['isAdmin'] = session()->get('role') === 'ADMIN' || session()->get('role') === 'SUPERADMIN' ? true : false;
		if (session()->get('role') === 'SUPERADMIN') $data['isSAdmin'] =  true;

		$data['clubs'] = $modelClubs->findAll();
        $data['msg'] = session()->getFlashdata('msg');

		echo view('templates/header', $data);
		echo view('pages/clubs');
		echo view('templates/footer');
	}

	public function update_club()
    {
		if ($this->request->getMethod() !== 'post') return;
		if (!empty($_FILES['club_img']['name'])) {
			$img = $this->request->getFile('club_img');
			if ($img->isValid() && ! $img->hasMoved()) {
				$img->move('./uploads/clubs',$img->getClientName());
			}
		} else {
			$img = null;
		}
	
		$modelClubs = new ClubsModel();
		$club_name = $this->request->getVar('club_name');

		$id = $this->request->getVar('club_id');
		$newData = [
			'club_name' => $this->request->getVar('club_name'),
			'club_description' => $this->request->getVar('club_description'),
		];
		if ($img) $newData['club_img'] = $img->getClientName();

		if ($modelClubs->update($id, $newData)) {
			$session = session();
			$session->setFlashdata('msg', "\"{$club_name}\" updated!");
			return redirect()->to(base_url('/clubs'));
		} else {
			$session = session();
			$session->setFlashdata('msg', "Error updating \"{$club_name}\"");
		}
    }

	public function delete_club()
	{
		if ($this->request->getMethod() == 'post') {
			$rules = [
				'club_id' => 'integer|matches[club_id]',
			];
			$club_id = $this->request->getVar('club_id');
			$club_name = $this->request->getVar('club_name');

			if (! $this->validate($rules)) {
                $data['validation'] = $this->validator;
                echo $club_id;
                echo $this->validator->listErrors();
			} else {
                $model = new ClubsModel();
				$modelClubUsers = new ClubUsersModel;
				$modelClubMessages = new ClubMessagesModel;
				$modelClubInvitations = new ClubInvitationsModel;

				$modelClubInvitations->where(['club_id' => $club_id])->delete();
				$modelClubMessages->where(['club_id' => $club_id])->delete();
				$modelClubUsers->where(['club_id' => $club_id])->delete();
                
				$model->delete($club_id);
				$session = session();
                $session->setFlashdata('msg', "{$club_name} was deleted");
                
				return redirect()->to('/clubs');
			}
		}
	}

	public function get_clubs() {
		$request = new Request();
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelClubs = new ClubsModel;
		$modelClubUsers = new ClubUsersModel;

		$postData = $this->request->getPost();

		$app_user_id = $modelAppUserRels->where(['profile_id' => $postData['profile_id']])->first()['app_user_id'];
		
		$my_clubs = $modelClubUsers->where(['app_user_id' => $app_user_id])->findAll();

		foreach( $my_clubs as $index => $club ) {
			$club_info = $modelClubs->where(['club_id' => $club['club_id']])->first();
			$my_clubs[$index]['club_name'] = $club_info['club_name'];
			$my_clubs[$index]['club_description'] = $club_info['club_description'];
			$my_clubs[$index]['club_img'] = $club_info['club_img'];
			$my_clubs[$index]['owner_user_id'] = $club_info['owner_user_id'];
		}

		return $this->response->setStatusCode(202)->setJSON(['clubList' => $my_clubs]);
	}

	public function add_club() {
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelClubs = new ClubsModel;
		$modelClubUsers = new ClubUsersModel;

		$postData = $this->request->getPost();

		// check validation
		$app_user_id = $modelAppUserRels->where(['profile_id' => $postData['profile_id']])->first()['app_user_id'];

		$my_clubs = $modelClubs->where(['owner_user_id' => $app_user_id])->findAll();

		if( count($my_clubs) >= 3 ) {
			return $this->response->setStatusCode(202)->setJSON(['message' => 'You cannot have more than 3 clubs.']);
		}

		// Check File
		if (!empty($_FILES['image']['name'])) {	
			$media = $this->request->getFile('image');
			if ($media->isValid() && ! $media->hasMoved()) {
				$media->move('./uploads/clubs',$media->getClientName());
			}
		} else {
			$media = null;
		}

		if( $media != null ) {
			$club_name = $postData['name'];
			$club_description = $postData['description'];
			$club_img = $media->getClientName();

			$club_id = $modelClubs->insert(['club_name' => $club_name, 'club_description' => $club_description, 'club_img' => $club_img, 'owner_user_id' => $app_user_id]);

			$modelClubUsers->insert(['app_user_id' => $app_user_id, 'club_id' => $club_id]);

			$my_clubs = $modelClubUsers->where(['app_user_id' => $app_user_id])->findAll();

			foreach( $my_clubs as $index => $club ) {
				$club_info = $modelClubs->where(['club_id' => $club['club_id']])->first();
				$my_clubs[$index]['club_name'] = $club_info['club_name'];
				$my_clubs[$index]['club_description'] = $club_info['club_description'];
				$my_clubs[$index]['club_img'] = $club_info['club_img'];
				$my_clubs[$index]['owner_user_id'] = $club_info['owner_user_id'];
			}

			return $this->response->setStatusCode(202)->setJSON(['success' => true, 'clubList' => $my_clubs]);
		}

		return $this->response->setStatusCode(400)->setJSON(["error"=>"Something went wrong."]);
	}

	public function get_club_detail() {
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelClubs = new ClubsModel;
		$modelClubUsers = new ClubUsersModel;
		$modelClubMessages = new ClubMessagesModel;
		$modelProfiles = new ProfilesModel;

		$postData = $this->request->getPost();

		$club_id = $postData['club_id'];

		$club_users_temp = $modelClubUsers->where(['club_id' => $club_id])->findAll();

        $club_users = [];

        foreach( $club_users_temp as $club_user ) {
            $app_user = $modelAppUser->where(['app_user_id' => $club_user['app_user_id']])->first();
            if( !isset($app_user) )
                continue;
			$app_user_rels = $modelAppUserRels->where(['app_user_id' => $club_user['app_user_id']])->first();
            if( !isset($app_user_rels) )
                continue;
            $profile_id = $modelAppUserRels->where(['app_user_id' => $club_user['app_user_id']])->first()['profile_id'];
			$profile = $modelProfiles->where(['profile_id' => $profile_id])->first();
            if( !isset($profile) )
                continue;

            array_push($club_users, $club_user);
        }

		foreach( $club_users as $key => $club_user ) {
			$user_name = $modelAppUser->where(['app_user_id' => $club_user['app_user_id']])->first()['app_user_name'];
			$profile_id = $modelAppUserRels->where(['app_user_id' => $club_user['app_user_id']])->first()['profile_id'];
			$user_ava = $modelProfiles->where(['profile_id' => $profile_id])->first()['profile_img_ava'];

			$club_users[$key]['user_name'] = $user_name;
			$club_users[$key]['user_ava'] = $user_ava;
		}

		$club_messages = $modelClubMessages->where(['club_id' => $club_id])->findAll();

		foreach( $club_messages as $key => $message ) {
			$profile_id = $modelAppUserRels->where(['app_user_id' => $message['app_user_id']])->first()['profile_id'];
			$club_messages[$key]['app_user_name'] = $modelAppUser->where(['app_user_id' => $message['app_user_id']])->first()['app_user_name'];
			$club_messages[$key]['app_user_ava'] = $modelProfiles->where(['profile_id' => $profile_id])->first()['profile_img_ava'];
		}

		return $this->response->setStatusCode(202)->setJSON(['users' => $club_users, 'messageList' => $club_messages]);
	}

	public function get_invite_users() {
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelClubs = new ClubsModel;
		$modelClubUsers = new ClubUsersModel;
		$modelClubInvitations = new ClubInvitationsModel;
		$modelProfiles = new ProfilesModel;
		$modelProfileRels = new Profile_relsModel;

		$postData = $this->request->getPost();
		
		$app_user_id = $postData['user_id'];
		$club_id = $postData['club_id'];
		$profile_id = $modelAppUserRels->where(['app_user_id' => $app_user_id])->first()['profile_id'];

		$friends = $modelProfileRels->where(['profile_id' => $profile_id])->findAll();
		$userList = [];
		foreach($friends as $key => $friend) {
			$temp = $modelClubUsers->where(['app_user_id' => $friend['app_user_id'], 'club_id' => $club_id])->find();
			if( count($temp) > 0 )
				continue;
			
			$temp = $modelClubInvitations->where(['app_user_id' => $friend['app_user_id'], 'club_id' => $club_id])->find();
			if( count($temp) > 0 )
				continue;

			$user = [];
			$user['app_user_id'] = $friend['app_user_id'];
			$user['profile_id'] = $modelAppUserRels->where(['app_user_id' => $user['app_user_id']])->first()['profile_id'];
			$user['user_name'] = $modelAppUser->where(['app_user_id' => $user['app_user_id']])->first()['app_user_name'];
			$user['user_ava'] = $modelProfiles->where(['profile_id' => $user['profile_id']])->first()['profile_img_ava'];

			array_push($userList, $user);
		}

		return $this->response->setStatusCode(202)->setJSON(['userList' => $userList]);
	}

	public function invite_user() {
		$oauth = new Oauth();
		$request = new Request();
		$modelAppUser = new AppUserModel;
		$modelAppUserRels = new AppUserRelsModel;
		$modelClubs = new ClubsModel;
		$modelClubUsers = new ClubUsersModel;
		$modelClubInvitations = new ClubInvitationsModel;
		$modelProfiles = new ProfilesModel;
		$modelProfileRels = new Profile_relsModel;

		$postData = $this->request->getPost();

		$app_user_id = $postData['user_id'];
		$club_id = $postData['club_id'];
		$invite_user_id = $postData['invite_user_id'];
		$profile_id = $modelAppUserRels->where(['app_user_id' => $app_user_id])->first()['profile_id'];

		$modelClubInvitations->insert(['club_id' => $club_id, 'app_user_id' => $invite_user_id]);

		$friends = $modelProfileRels->where(['profile_id' => $profile_id])->findAll();
		$userList = [];
		foreach($friends as $key => $friend) {
			$temp = $modelClubUsers->where(['app_user_id' => $friend['app_user_id'], 'club_id' => $club_id])->find();
			if( count($temp) > 0 )
				continue;
			
			$temp = $modelClubInvitations->where(['app_user_id' => $friend['app_user_id'], 'club_id' => $club_id])->find();
			if( count($temp) > 0 )
				continue;

			$user = [];
			$user['app_user_id'] = $friend['app_user_id'];
			$user['profile_id'] = $modelAppUserRels->where(['app_user_id' => $user['app_user_id']])->first()['profile_id'];
			$user['user_name'] = $modelAppUser->where(['app_user_id' => $user['app_user_id']])->first()['app_user_name'];
			$user['user_ava'] = $modelProfiles->where(['profile_id' => $user['profile_id']])->first()['profile_img_ava'];

			array_push($userList, $user);
		}

		return $this->response->setStatusCode(202)->setJSON(['userList' => $userList]);
	}

	public function invitation_action() {
		$oauth = new Oauth();
		$request = new Request();
		$modelClubs = new ClubsModel;
		$modelClubInvitations = new ClubInvitationsModel;
		$modelClubUsers = new ClubUsersModel;
		$modelNotification = new NotificationModel;

		$postData = $this->request->getPost();
		$invitation_id = $postData['notification_id'];
		$accept = $postData['accept'];

		$app_user_id = $modelClubInvitations->where(['id' => $invitation_id])->first()['app_user_id'];
		$club_id = $modelClubInvitations->where(['id' => $invitation_id])->first()['club_id'];

		if( $accept === "1" ) {
			$modelClubUsers->insert(['app_user_id' => $app_user_id, 'club_id' => $club_id]);
			$message = "You've accepted the invitation.";
		} else {
			$message = "You've declined the invitation.";
		}

		$modelClubInvitations->where(['id' => $invitation_id])->delete();

		$notifications = $modelClubInvitations->where(['app_user_id' => $app_user_id])->findAll();
		foreach( $notifications as $key => $notification ) {
			$notifications[$key]['club_name'] = $modelClubs->where(['club_id' => $notification['club_id']])->first()['club_name'];
			$notifications[$key]['type'] = 'club_invitation';
		}

		$temp = $modelNotification->where(['app_user_id' => $app_user_id, 'read' => '0'])->orderBy('created_at', 'desc')->findAll();
		foreach( $temp as $key => $tmp ) {
			$tmp['type'] = 'notify';
			array_push($notifications, $tmp);
		}

		return $this->response->setStatusCode(202)->setJSON(['notifications' => $notifications, 'message' => $message]);
	}

	public function add_club_message() {
		$oauth = new Oauth();
		$request = new Request();
		
		$modelAppUserRels = new AppUserRelsModel;
		$modelClubMessages = new ClubMessagesModel;
		$modelAppUser = new AppUserModel;
		$modelProfiles = new ProfilesModel;
		
		$postData = $this->request->getPost();

		$profile_id = $postData['profile_id'];
		$club_id = $postData['club_id'];
		$content = $postData['content'];
		$sent_at = $postData['sent_at'];

		$app_user_id = $modelAppUserRels->where(['profile_id' => $profile_id])->first()['app_user_id'];
		
		$modelClubMessages->insert(['app_user_id' => $app_user_id, 'club_id' => $club_id, 'content' => $content, 'sent_at' => $sent_at]);

		$messageList = $modelClubMessages->where(['club_id' => $club_id])->findAll();

		foreach( $messageList as $key => $message ) {
			$profile_id = $modelAppUserRels->where(['app_user_id' => $message['app_user_id']])->first()['profile_id'];
			$messageList[$key]['app_user_name'] = $modelAppUser->where(['app_user_id' => $message['app_user_id']])->first()['app_user_name'];
			$messageList[$key]['app_user_ava'] = $modelProfiles->where(['profile_id' => $profile_id])->first()['profile_img_ava'];
		}

		return $this->response->setStatusCode(202)->setJSON(['messageList' => $messageList]);
	}

	public function remove_friend() {
		$oauth = new Oauth();
		$request = new Request();
	
		$modelAppUserRels = new AppUserRelsModel;
		$modelAppUser = new AppUserModel;
		$modelProfiles = new ProfilesModel;
		$modelProfileRels = new Profile_relsModel;

		$postData = $this->request->getPost();

		$profile_id = $postData['profile_id'];
		$app_user_id = $postData['app_user_id'];

		$modelProfileRels->where(['profile_id' => $profile_id, 'app_user_id' => $app_user_id])->delete();

		$friends = $modelProfileRels->where(['profile_id' => $profile_id])->findAll();

		foreach($friends as $key => $friend) {
			$friends[$key] = [ 'app_user_id' => $friend['app_user_id'], 'profile_rel_status' => $friend['profile_rel_status'] ];
			$friends[$key]['app_user_name'] = $modelAppUser->where(['app_user_id' => $friend['app_user_id']])->first()['app_user_name'];
			$profile_id = $modelAppUserRels->where(['app_user_id' => $friend['app_user_id']])->first()['profile_id'];
			$friends[$key]['profile'] = $modelProfiles->where(['profile_id' => $profile_id])->first();
		}

		return $this->response->setStatusCode(202)->setJSON(['friends' => $friends]);
	}
}