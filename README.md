#FakeMart
###Start Project
```
#install vendor file
composer install --ignore-platform-reqs
```

```
#migrate table and create sample row
php artisan migrate:fresh --seed
```

```
#start project in develoment mode
php artisan serve
```

###Database Structure
_FakeMart_
````
1. branch
 - id (pk)
 - name (varchar)*
 - location(varchar)*
 - contact_number(varchar)*

2. position
 - id (pk)
 - branch_id (fk)*
 - name (varchar)*
 - description (varchar)

3. staff
 - id (pk)
 - position_id (fk)*
 - name (varchar)*
 - gender (varchar)*
 - dob (date)*
 - pob (varchar)*
 - address (varchar)*
 - phone(varchar)*
 - nation_id_card (varchar)*

4. user
 - id (pk)
 - username (varchar)*
 - password (varchar)*
 - staff_id (fk)*

5. category
 - id (pk)
 - name (varchar)*
 - description (varchar)

6. product
 - id (pk)
 - name (varchar)*
 - cost (float)*
 - price (float)*
 - image (varchar)
 - description (varchar)
 - category_id (fk)*

7. invoice
 - id (pk)
 - user_id (fk)*
 - created_at (datetime)*
 - total(float)*

8. invoice_item
 - id (pk)
 - invoice_id (fk)*
 - product_id (fk)*
 - qty (int)*
 - price (float)*
````
# su14_23_API
