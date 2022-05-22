# API Coding Challenge
project made with laravel/lumen and redis !

## Instructions

1. POST /reset
	* Reset state before starting tests	

2. GET /balance
	* Get balance for account
		GET /balance?account_id=1234

3. POST /event
	* Create or Deposit into an account
		POST /event {"type":"deposit", "destination":"100", "amount":10}