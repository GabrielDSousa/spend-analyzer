<p align="center"><a href="#" target="_blank"><img src="https://raw.githubusercontent.com/GabrielDSousa/arts/master/flatwallet.svg" width="200" alt="Laravel Logo"></a></p>

<p align="center">
<a href="#"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# About Spend Analyzer API

Laravel is an api for register incomes and expenses. Manually or with a csv bank statement.
The api have a filter to make personalized metrics for the transactions.

## Using the API
All request need to have the rule below on header
````json
{
    "Accept":"application/json"
}
````
For the exceptions returns be in json format.

###Create route
<b>URI</b>

````
POST
/api/transactions/
````

<b>Body</b>
````json
{
	"date": "Y-m-d",
	"amount": float,
	"description": string,
	"file": string|nullable,
	"type": string,
	"bank": string
}
````

<b>Responses</b>
- Transaction object
- Validation error from the body malformed
- Database error
- Server error

###All route
<b>URI</b>

````
GET
/api/transactions/all
````

<b>URI filters</b>
- Stackable:
    - date: On the format Y-m-d, will return all transaction of that date;
    - description: The partial word that will be searched on description;
    - file: Name of the file that the transaction came, if exists;
    - type: If was debit, credit, or extra
    - bank: The bank where the transaction came
    - [wip] start: On the format Y-m-d, Get all transactions after this date;
    - [wip] start: On the format Y-m-d, Get all transactions before this date;
    - [wip] incomes: Get all transactions with positive amount;
    - [wip] expenses: Get all transactions with negative amount;

<b>Responses</b>
- List of Transactions based on the given filter, list will be paginated.
- Database error
- Server error

###Read route
<b>URI</b>

````
/api/transactions/{id}
````

<b>Responses</b>
- Transaction object
- Transaction not found
- Database error
- Server error

###Update route
<b>URI</b>

````
PUT
/api/transactions/{id}
````

<b>Body</b>
Could have any or none of the fields below:
````json
{
	"date": "Y-m-d",
	"amount": float,
	"description": string,
	"file": string,
	"type": string,
	"bank": string
}
````

<b>Responses</b>
- The updated Transaction object
- Validation error from the body malformed
- Transaction not found
- Database error
- Server error

###Delete route
<b>URI</b>

````
DELETE
/api/transactions/{id}
````

<b>Responses</b>
- Success
- Transaction not found
- Database error
- Server error

## Roadmap
* [ ] Auth with user
* [ ] Transactions by user
* [ ] New filters
* [ ] Read CSV and transform in transactions
* [ ] NuBank and Inter maps
* [ ] A custom map for CSV
* [ ] Deploy to Heroku


## License

The api Spend Analyzer is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
