const user_id = localStorage.getItem('user_id')
const span_first_name = document.getElementById('profile_header_first_name')
const first_name_td = document.getElementById('first_name_td')
const last_name_td = document.getElementById('last_name_td')
const email_td = document.getElementById('email_td')
const phone_td = document.getElementById('phone_td')
const sent_bal = document.getElementById('sent_balance')
const received_bal = document.getElementById('received_balance')
const total_bal = document.getElementById('total_balance')
const deposit_button = document.getElementById('deposit-button')
const withdraw_button = document.getElementById('withdraw-button')
const transfer_button = document.getElementById('transfer-button')
const deposit_section = document.getElementById('deposit_section')
const btn_cancel_deposit = document.getElementById('cancel-deposit')
const btn_submit_deposit = document.getElementById('submit-deposit')
const deposit_amount = document.getElementById('deposit-name-input')
const wallets_dropdown = document.getElementById('wallets-dropdown')
const wallets_dropdown_withdraw = document.getElementById(
  'wallets-dropdown-withdraw'
)
const withdraw_section = document.getElementById('withdraw_section')
const withdraw_amount_input = document.getElementById('withdraw-amount-input')
const withdraw_card_input = document.getElementById('withdraw-card-input')
const withdraw_pin_input = document.getElementById('withdraw-pin-input')
const btn_cancel_withdraw = document.getElementById('cancel-withdraw')
const btn_submit_withdraw = document.getElementById('submit-withdraw')
const btn_cancel_transfer = document.getElementById('cancel-transfer')
const btn_submit_transfer = document.getElementById('submit-transfer')
const transfer_section = document.getElementById('transfer_section')
const transfer_amount_input = document.getElementById('transfer-amount-input')

const searchValue = document.getElementById('searchEmail')
const dropdown = document.getElementById('emailDropdown')
const options = dropdown.getElementsByTagName('option')
const wallets_transfer_inp = document.getElementById('wallets_dropdown-sel')

const all_users = []

window.onload = async function () {
  if (!user_id) {
    window.location.href = '../user/login.html'
    return
  }
  await getUserDetails()
  await getUserTransactions()
  await getUserWallets()
  await getAllUsers()
}

async function getUserDetails() {
  try {
    const response = await api.get(
      `/api/v1/users/getAllUsers.php?id=${user_id}`
    )

    if (response.data.status !== 'success') {
    }
    span_first_name.innerHTML = response.data.data.first_name + "'s "
  } catch (error) {
    console.log(error)
  }
}

async function getUserWallets() {
  try {
    const response = await api.get(
      `/api/v1/wallets/getWalletsByUser.php?user_id=${user_id}`
    )

    if (response.data.status !== 'success') {
      alert(response.data.message)
      return
    }

    const all_wallets = response.data.data

    wallets_dropdown.innerHTML = ''
    const defaultOption = document.createElement('option')
    defaultOption.textContent = 'Select A wallet'
    defaultOption.value = ''
    wallets_dropdown.appendChild(defaultOption)

    wallets_dropdown_withdraw.innerHTML = ''
    const defaultOptionW = document.createElement('option')
    defaultOptionW.textContent = 'Select A wallet'
    defaultOptionW.value = ''
    wallets_dropdown_withdraw.appendChild(defaultOptionW)

    all_wallets.forEach((wallet) => {
      const option = document.createElement('option')
      option.textContent = wallet.name
      option.value = wallet.id
      wallets_dropdown.appendChild(option)

      wallets_dropdown_withdraw.appendChild(option.cloneNode(true))
      wallets_transfer_inp.appendChild(option.cloneNode(true))
    })
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

    const transactionsBody = document.getElementById('transactions-table')
    transactionsBody.innerHTML = ''

    all_transactions.forEach((item) => {
      const row = document.createElement('tr')
      const typeCell = document.createElement('td')
      typeCell.textContent = item.type
      row.appendChild(typeCell)

      const amountCell = document.createElement('td')
      amountCell.innerHTML = `$<span>${item.amount}</span>`
      row.appendChild(amountCell)

      const senderCell = document.createElement('td')
      senderCell.textContent = item.sender_wallet_id || 'Bank'
      senderCell.classList.add('hidee')
      row.appendChild(senderCell)

      const recipientCell = document.createElement('td')
      recipientCell.textContent = item.recipient_wallet_id || 'Bank'
      recipientCell.classList.add('hidee')

      row.appendChild(recipientCell)

      const dateCell = document.createElement('td')
      dateCell.textContent = item.date_time || 'Unknown'
      dateCell.classList.add('hidee')

      row.appendChild(dateCell)

      transactionsBody.appendChild(row)

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

deposit_button.addEventListener('click', function (event) {
  event.preventDefault()

  deposit_section.classList.toggle('hidden')
  withdraw_section.classList.add('hidden')
  transfer_section.classList.add('hidden')
})

btn_cancel_deposit.addEventListener('click', function (event) {
  event.preventDefault()
  deposit_section.classList.add('hidden')
})

btn_submit_deposit.addEventListener('click', async function (event) {
  event.preventDefault()

  try {
    const response = await api.post('/api/v1/transactions/addTransaction.php', {
      type: 'deposit',
      user_id: user_id,
      amount: deposit_amount.value,
      sender_wallet_id: wallets_dropdown.value,
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

withdraw_button.addEventListener('click', function (event) {
  event.preventDefault()
  withdraw_section.classList.toggle('hidden')
  deposit_section.classList.add('hidden')
  transfer_section.classList.add('hidden')
})

btn_cancel_withdraw.addEventListener('click', function (event) {
  event.preventDefault()
  withdraw_section.classList.add('hidden')
})

btn_submit_withdraw.addEventListener('click', async function (event) {
  event.preventDefault()

  try {
    const response = await api.post('/api/v1/transactions/addTransaction.php', {
      type: 'withdraw',
      user_id: user_id,
      amount: withdraw_amount_input.value,
      sender_wallet_id: wallets_dropdown_withdraw.value,
      card_number: withdraw_card_input.value,
      pin: withdraw_pin_input.value,
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

transfer_button.addEventListener('click', async function (event) {
  event.preventDefault()
  transfer_section.classList.toggle('hidden')
  deposit_section.classList.add('hidden')
  withdraw_section.classList.add('hidden')
})

btn_cancel_transfer.addEventListener('click', function (event) {
  event.preventDefault()
  transfer_section.classList.add('hidden')
})

async function getAllUsers() {
  try {
    const response = await api.get(`/api/v1/users/getAllUsers.php?`)

    if (response.data) {
      response.data.forEach((element) => {
        all_users.push({ email: element.email, id: element.id })

        const option = document.createElement('option')
        option.value = element.email
        option.textContent = element.email
        dropdown.appendChild(option)
      })
    } else {
      console.log('Failed to fetch users:', response.data)
    }
  } catch (error) {
    console.log('Error fetching users:', error)
  }
}

function filterEmailDropDwon() {
  const serch_value = searchValue.value
  dropdown.size = 5
  for (let i = 0; i < options.length; i++) {
    const optionText = options[i].text.toLowerCase()
    if (optionText.includes(serch_value)) {
      options[i].style.display = 'block'
    } else {
      options[i].style.display = 'none'
    }
  }
}
function updateEmailInput() {
  const selectedEmail = dropdown.value
  document.getElementById('searchEmail').value = selectedEmail
}

btn_submit_transfer.addEventListener('click', async function (event) {
  event.preventDefault()

  try {
    const response = await api.post('/api/v1/transactions/addTransaction.php', {
      type: 'transfer',
      user_id: user_id,
      amount: transfer_amount_input.value,
      recipient_wallet_id: searchValue.value,
      sender_wallet_id: wallets_transfer_inp.value,
    })
    if (response.data.status !== 'success') {
      alert(response.data.message)
    }

    console.log('wallet add', response.data)
    if (response.data.status !== 'success') {
      console.log(response.data)
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
