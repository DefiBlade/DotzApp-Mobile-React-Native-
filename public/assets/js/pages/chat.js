const app_users = document.querySelectorAll('#user-list>li')
const chat_wndow = document.querySelector('#chat_wndow')
const chat_name = document.querySelector('#chat_name>a')

const conn = new WebSocket(`${pageData.chat_server_con_prot}://${pageData.chat_server_domain}:${pageData.chat_server_port}?access_token=${pageData.user_id}`);

let liveInterval = []

app_users.forEach(uItem => {
  uItem.addEventListener('click', selectUserChat)
  let timer = $(uItem).find('.media .font-size-11').data('msg_latest_timestamp')
  if (timer) {
    let unreadDate = new Date(timer)
    liveInterval[uItem.dataset.app_user_id] = msgSetInt(uItem, unreadDate)
  }
})

$(function () {
    scrollMsgBottom()
})

conn.onopen = function(e) {
  document.querySelector('#connStatusColor').classList.remove('text-warning')
  document.querySelector('#connStatusColor').classList.add('text-success')
  document.querySelector('#connStatusText').innerText = "online"
  // console.log("Connection established!");
  console.log(e);
};

conn.onmessage = function(e) {
  var data = JSON.parse(e.data)
  console.log(data)

  if ('error' in data) {
    // console.log(data)
  }
  
  if ('users' in data){
    updateUsers(data.users)
  } else if('message' in data){
    if (chat_wndow.dataset.app_user_id === data.author_id) {
      newMessage(data)
    } else {
      addUnreadMsg(data)
    }
  }

};

conn.onclose = function (e) {
  document.querySelector('#connStatusColor').classList.remove('text-success')
  document.querySelector('#connStatusText').innerText = "Disconnected - pls, try to reload the page"
  console.log(e)
  console.log(conn.readyState)
  if (e.code === 4000) {
    document.querySelector('#connStatusText').innerText = "Disconnected - pls, try relogging"
  }
}

conn.onerror = function (e) {
  console.log(e,'error')
}

$('#send').on('click', sendMsg)
$('#message-input').on('keyup', e=>{
  if (e.keyCode === 13) {
    e.preventDefault()
    sendMsg()
  }
})

function newMessage(msg, msgQty = 1){
  let msgTime = new Date(msg.msg_timestamp_sent)
  let msgTimeSent = `${msgTime.getHours()}:${ msgTime.getMinutes() < 10 ? '0'+msgTime.getMinutes() : msgTime.getMinutes() }`

  let data = {
    name: msg.author_name,
    time: msgTimeSent,
    msg: msg.message,
  }
  $('#messages .simplebar-content').append(msgHtml(data,'left'))
  if (msgQty === 1) scrollMsgBottom()

}

function myMessage(msg, msgQty = 1){
  var date = new Date;
  var minutes = date.getMinutes();
  var hour = date.getHours();
  var time = hour + ':' + minutes

  let data = {
    name: pageData.user_name,
    time: time,
    msg: msg,
  }
  $('#messages .simplebar-content').append(msgHtml(data,'right'))
  if (msgQty === 1) scrollMsgBottom()
}

function msgHtml(data, position = 'left') {
  position = position !== 'left' ? ' class="right"' : ''
  let html = `
    <li${position}>
        <div class="conversation-list">
            <div class="ctext-wrap">
                <div class="ctext-wrap-content">
                    <h5 class="font-size-14 conversation-name">
                        <a href="#" class="text-dark">${data.name}</a>
                        <span class="d-inline-block font-size-12 text-muted ml-2">${data.time}</span>
                    </h5>
                    <p class="mb-0">${data.msg}</p>
                </div>
                <div class="dropdown align-self-start">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="uil uil-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">Copy</a>
                        <a class="dropdown-item" href="#">Save</a>
                        <a class="dropdown-item" href="#">Forward</a>
                        <a class="dropdown-item" href="#">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </li$>
  `
  return html
}

function updateUsers(users){
  var html = ''
  var myId = pageData.user_id
  
  
  for (let index = 0; index < users.length; index++) {
    if(myId != users[index].c_user_id) {
      html += `
        <li>
          <a href="#">
              <div class="media">
                  
                  <div class="user-img online align-self-center mr-3">
                      <div class="avatar-xs align-self-center">
                          <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                              V
                          </span>
                      </div>
                      <span class="user-status"></span>
                  </div>
                  
                  <div class="media-body overflow-hidden">
                      <h5 class="text-truncate font-size-14 mb-1">${users[index].c_name}</h5>
                      <p class="text-truncate mb-0">Wow that's great</p>
                  </div>
                  <div class="font-size-11">04 Hrs</div>
              </div>
          </a>
        </li>
      `
    }
  }

  if(html == ''){
    html = '<p>The Chat Room is Empty</p>'
  }

  app_users.forEach(el=>{
    for (let index = 0; index < users.length; index++) {
      if(el.dataset.app_user_id === users[index].c_user_id) {
        console.log(el.dataset.app_user_id+' is online')
        $(el).find('.user-img').addClass('online')
      }
    }
  })

  document.querySelector('#usersOnline').innerText = users.length

  // $('#user-list').append(html)
}

function sendMsg() {
  var msg = $('#message-input').val()
  let time_sent = new Date
  let data = {
    msg_reciever_id:chat_wndow.dataset.app_user_id,
    msg: msg,
    msg_timestamp_sent: time_sent.getTime(),
    msg_time_sent: time_sent.toMysqlFormat(),
  }

  if(msg.trim() == '') return false
    
  console.log(data)

  conn.send( JSON.stringify(data) );
  myMessage(msg)
  $('#message-input').val('')
}

function selectUserChat(e) {
  // console.log(e.currentTarget)
  let $userListItem = $(e.currentTarget);
  chat_name.innerText = e.currentTarget.dataset.app_user_name
  chat_wndow.dataset.app_user_id = e.currentTarget.dataset.app_user_id

  $.ajax({
    url: "/chat/getChatHistory",
    type: 'post',
    dataType:'json',
    data:{app_user_id:e.currentTarget.dataset.app_user_id},
    headers: {'X-Requested-With': 'XMLHttpRequest'}
  })
  .done(function (result) {
    // console.log(result)
    $('#messages .simplebar-content').html('')
    result.forEach(rItem=>{
      let position = rItem.author_type !== 'admin' ? 'left' : 'right'
      let msgTime = new Date(rItem.msg_timestamp_sent)
      let data = {
        name: rItem.author_name,
        time: `${msgTime.getHours()}:${msgTime.getMinutes() < 10 ? '0'+msgTime.getMinutes() : msgTime.getMinutes() }`,
        msg: rItem.message,
      }
      $('#messages .simplebar-content').append(msgHtml(data, position))
    })
    scrollMsgBottom(0)
    
    $userListItem.find('.unread-message').remove()
    $userListItem.removeClass('unread')
  })
}

function addUnreadMsg(data) {
  app_users.forEach(uItem => {
    if (uItem.dataset.app_user_id === data.author_id) {
      let $unread = $(uItem).find('.unread-message')
      let unreadDate = new Date(data.msg_timestamp_sent)
      clearInterval(liveInterval[data.author_id])
      liveInterval[data.author_id] = msgSetInt(uItem,unreadDate)

      uItem.classList.add('unread')
      $(uItem).find('.media-body.overflow-hidden>p').text(data.message.substring(0, 36))
      
      if (!$unread.length>0) {
        let html = `
          <div class="unread-message">
            <span class="badge badge-danger badge-pill">01</span>
          </div>
        `
        $(uItem).find('.media').append(html)
      } else {
        let unreadQty = $unread.find('.badge-pill').text()
        $unread.find('.badge-pill').text(++unreadQty)
      }
    }
  })
}

function msgSetInt(uItem,unreadDate) {
  return setInterval(function() {
    let now = new Date().getTime()
    let distance = now - unreadDate
    let days = Math.floor(distance / (1000 * 60 * 60 * 24));
    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    let msgTime = ''
    if (days) {
      msgTime = `${days < 10 ? '0'+days  : days} days`
    } else if(hours) {
      msgTime = `${hours < 10 ? '0'+hours  : hours} Hrs`
    } else if(minutes) {
      msgTime = `${minutes < 10 ? '0'+minutes  : minutes} Min`
    } else {
      msgTime = '<Min'
    }
    
    $(uItem).find('.media .font-size-11').text(msgTime)
    // console.log(msgTime)
  }, 1000);
}

function scrollMsgBottom(scrollSpeed = 'slow'){
    $("#messages .simplebar-content-wrapper").animate({ scrollTop: $('#messages .simplebar-content').height() }, scrollSpeed)
}

/**
 * You first need to create a formatting function to pad numbers to two digits…
 **/
function twoDigits(d) {
  if(0 <= d && d < 10) return "0" + d.toString();
  if(-10 < d && d < 0) return "-0" + (-1*d).toString();
  return d.toString();
}
Date.prototype.toMysqlFormat = function() {
  return this.getFullYear() + "-" + twoDigits(1 + this.getMonth()) + "-" + twoDigits(this.getDate()) + " " + twoDigits(this.getHours()) + ":" + twoDigits(this.getMinutes()) + ":" + twoDigits(this.getSeconds());
};