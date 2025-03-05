const user_id = localStorage.getItem('user_id')
const span_first_name = document.getElementById('profile_header_first_name')
const first_name_td = document.getElementById('first_name_td')
const last_name_td = document.getElementById('last_name_td')
const email_td = document.getElementById('email_td')
const phone_td = document.getElementById('phone_td')
const sent_bal = document.getElementById('sent_balance')
const received_bal = document.getElementById('received_balance')
const total_bal = document.getElementById('total_balance')

window.onload = async function () {
  if (!user_id) {
    window.location.href = '../user/login.html'
    return
  }
  await getUserDetails()
  await getUserTransactions()
}

async function getUserDetails() {
  try {
    const response = await api.get(
      `/api/v1/users/getAllUsers.php?id=${user_id}`
    )

    if (response.data.status !== 'success') {
      alert(response.data.message)
      return
    }
    span_first_name.innerHTML = response.data.data.first_name + "'s "
    first_name_td.innerHTML = response.data.data.first_name
    last_name_td.innerHTML = response.data.data.last_name
    email_td.innerHTML = response.data.data.email
    phone_td.innerHTML = response.data.data.phone
  } catch (error) {
    console.log(error)
  }
}

async function getUserTransactions() {
  try {
    const response = await api.get(
      `/api/v1/transactions/getAllTransactionsByUser.php?user_id=${user_id}`
    )

    if (response.data.status !== 'success') {
      alert(response.data.message)
      return
    }

    const all_transactions = response.data.data
    let sent_balance = 0
    let received_balance = 0

    all_transactions.forEach((item) => {
      if (
        item.type === 'deposit' ||
        (item.type === 'transfer' && item.user_id != user_id)
      ) {
        received_balance += item.amount
      } else if (
        item.type === 'withdraw' ||
        (item.type === 'transfer' && item.user_id == user_id)
      ) {
        sent_balance += item.amount
      }
    })
    total_bal.innerHTML = received_balance - sent_balance
    sent_bal.innerHTML = sent_balance
    received_bal.innerHTML = received_balance
  } catch (error) {
    console.log(error)
  }
}

async function userLoginApi(event) {
  event.preventDefault()

  try {
    const response = await api.post('/api/v1/users/signin.php', {
      email: email_address.value,
      password: password.value,
    })

    if (response.data.status === 'success') {
      localStorage.setItem('user_id', response.data.data.id)
      window.location.href = '/user/profile.html'
    }
  } catch (error) {
    console.log(error)
  }
}

const logutButton = document.getElementById('logout')
logutButton.addEventListener('click', function () {
  localStorage.removeItem('user_id')
  window.location.href = '../index.html'
})
