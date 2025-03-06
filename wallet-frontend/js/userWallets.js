const user_id = localStorage.getItem('user_id')
const span_first_name = document.getElementById('firs_name_span')
const btn_add_new_wallet = document.getElementById('btn-add-new-wallet')
const add_wallet_section = document.getElementById('add_wallet_section')
const btn_cancel_wallet = document.getElementById('cancel-wallet')
const btn_submit_wallet = document.getElementById('submit-wallet')
const wallet_name = document.getElementById('wallet-name-input')
let email
let phone

window.onload = async function () {
  if (!user_id) {
    window.location.href = '../user/login.html'
    return
  }
  await getUserDetails()
  await getUserWallets()
}

async function getUserDetails() {
  try {
    const response = await api.get(
      `/api/v1/users/getAllUsers.php?id=${user_id}`
    )

    if (response.data.status !== 'success') {
    }
    span_first_name.innerHTML = response.data.data.first_name + "'s "

    email = response.data.data.email
    phone = response.data.data.phone
  } catch (error) {
    console.log(error)
  }
}

async function getUserWallets() {
  try {
    const response = await api.get(
      `/api/v1/Wallets/getWalletsByUser.php?user_id=${user_id}`
    )

    if (response.data.status !== 'success') {
      alert(response.data.message)
      return
    }

    const all_wallets = response.data.data
    const walletCardContainer = document.getElementById('wallet-card-container')

    walletCardContainer.innerHTML = ''

    all_wallets.forEach((wallet) => {
      const walletContainerDiv = document.createElement('div')
      walletContainerDiv.classList.add('wallet-container')

      const walletDiv = document.createElement('div')
      walletDiv.classList.add('wallet-Shape')
      walletDiv.innerHTML = `
        <p>${wallet.name}</p>
        <p class="wallet-page-card-email">${email}</p>
        <p>$${wallet.balance}</p>
      `

      const cardDiv = document.createElement('div')
      cardDiv.classList.add('wallet-Shape')
      cardDiv.innerHTML = `
        <p>${wallet.name} Card</p>
        <p class="wallet-page-card-email">${phone}</p>
        <p>PIN ${wallet.id}</p>
      `

      walletContainerDiv.appendChild(walletDiv)
      walletContainerDiv.appendChild(cardDiv)

      walletCardContainer.appendChild(walletContainerDiv)
    })
  } catch (error) {
    console.log(error)
  }
}

btn_add_new_wallet.addEventListener('click', function (event) {
  event.preventDefault()
  add_wallet_section.classList.toggle('hidden')
})

btn_cancel_wallet.addEventListener('click', function (event) {
  event.preventDefault()
  add_wallet_section.classList.add('hidden')
})

btn_submit_wallet.addEventListener('click', async function (event) {
  event.preventDefault()

  try {
    const response = await api.post('/api/v1/Wallets/AddWallet.php', {
      user_id: user_id,
      name: wallet_name.value,
    })

    if (response.data.status !== 'success') {
      alert(response.data.message)
      return
    }
    window.location.reload()
  } catch (error) {
    console.log(error)
  }
})


const logutButton = document.getElementById('logout')
logutButton.addEventListener('click', function () {
  localStorage.removeItem('user_id')
  window.location.href = '../index.html'
})