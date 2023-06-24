# landshomesrepo
installation instructions

composer install

php artisan migrate

end points
POST http://localhost:8000/api/register
POST http://localhost:8000/api/login
POST http://localhost:8000/api/products  (create products0
GET http://localhost:8000/api/products  (get all the products)
GET http://localhost:8000/api/products/12 (get single Product)
PUT http://localhost:8000/api/products/12 (update product)
DELETE http://localhost:8000/api/products/12 (delete product)

CRON JOB

GET http://localhost:8000/api/products/destroypast (authentication is not required)



