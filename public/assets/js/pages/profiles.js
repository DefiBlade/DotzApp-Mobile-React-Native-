
(function ($) {
    // console.log($('.profile-verified-status'))
    $('.profile-verified-status').each((i,e)=>{
        // console.log(i,e)
        $(e).click(ev=>{
            console.log(ev)
            $.post('/profiles/verify',{profile_id: ev.target.name.split('_')[1], profile_verified: +ev.target.checked}).done(result => {
                console.log(result)
            })
        })
    })
})(jQuery)