let $ = jQuery

document.addEventListener('DOMContentLoaded', () => {
    $('.vote').click( e => {
        e.preventDefault()
        $.ajax({
            url: simpleVoting.ajax_url,
            type: 'POST',
            data: {
                'action': 'sv_save_vote',
                'post': simpleVoting.post_id,
                'nonce':  simpleVoting.nonce,
            },
            dataType : 'json',
            success:function(data) {
                if (data.data) {
                    document.getElementById('votePanel').classList.remove('wait')
                }
            },
            error: function(errorThrown){
                console.log(errorThrown.responseText);
            }
        })
    })
})