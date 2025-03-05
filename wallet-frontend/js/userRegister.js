const first_name = document.getElementById('first_name')
const last_name = document.getElementById('last_name')
const email_address = document.getElementById('email')
const password = document.getElementById('password')
const phone = document.getElementById('phone')

async function userRegisterApi(event) {
  event.preventDefault()

  try {
    const response = await api.post('/api/v1/users/signup.php', {
      first_name: first_name.value,
      last_name: last_name.value,
      email: email_address.value,
      password: password.value,
      phone: phone.value,
    })

    if (response.data.status === 'success') {
      localStorage.setItem('user_id', response.data.data.id)
      window.location.href = '/user/profile.html'
    } else {
      alert(response.data.message)
      return
    }
  } catch (error) {
    console.log(error)
  }
}
