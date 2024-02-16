<script nonce="{{nonce}}">
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

    document.querySelectorAll('.usr-del-btn[data-id]').forEach(button => {
        button.addEventListener('click', function() {
            if (!confirm('{{lang=confirm_delete}}')) return

            fetch(`/user/${button.dataset.id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': token
                }
            }).then(response => {
                response.clone().json().then(json => {
                    token = json?.token

                    if (!json?.ok) {
                        if (json?.redirect) return window.location.href = json?.redirect
                        return dialog(json?.title || `{{lang=error}}`, json?.text)
                    }

                    window.location.href = json?.redirect || `/`
                }).catch(error => {
                    response.text().then(text => {
                        dialog(`{{lang=error}}`, text)
                    })
                })
            })
        })
    })
</script>