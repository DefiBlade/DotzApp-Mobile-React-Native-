<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do something here
        if(! session()->get('isLoggedIn')){
          return redirect()->to('/');
        }
        if ($request->uri->getSegment(1) === 'delete_driver') {
            if(!session()->get('role') === 'ADMIN' || !session()->get('role') === 'SUPERADMIN') {
                return redirect()->to('drivers');
            }
        }

    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}