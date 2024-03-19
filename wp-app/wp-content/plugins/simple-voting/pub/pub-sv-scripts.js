let $ = jQuery,
    $voteButtons = $('.sv-panel span:not(:first-child)')
document.addEventListener('DOMContentLoaded', () => {
    $voteButtons.click(e => {
        e.preventDefault()
        $voteButtons.addClass('disabled')
        e.target.classList.add('voted')
        $.ajax({
            url: simpleVoting.ajax_url,
            type: 'POST',
            data: {
                'action': 'sv_save_vote',
                'post': simpleVoting.post_id,
                'nonce': simpleVoting.nonce,
                'vote': e.target.id
            },
            dataType: 'json',
            success: function (resp) {
                if (resp.data) {
                    let stats = resp.data.split(','),
                        yesStat = Math.round((stats[1] * 100) / stats[0]),
                        noStat = 100 - yesStat

                    displayVotingStats(yesStat, noStat)
                    saveCookies(e.target.id)
                }
            },
            error: function (errorThrown) {
                console.log(errorThrown.responseText);
            }
        })
    })
})

function displayVotingStats(yes, no) {
    document.getElementById('vote-up').innerHTML = yes + '%'
    document.getElementById('vote-down').innerHTML = no + '%'
    document.querySelector('.sv-panel span:first-child').innerHTML = simpleVoting.text
}

function saveCookies(id) {
    let path = window.location.pathname
    document.cookie = `alreadyVoted=${id}; expires=Fri, 31 Dec 2199 23:59:59 GMT; path=${path}`
}