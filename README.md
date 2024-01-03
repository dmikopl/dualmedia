# dualmedia

Dodawanie produktów

Przykładowy request do dodania produktu

POST ROUTE: http://localhost/ware/new

{
  "name": "Sample Product",
  "sku": "ABC123",
  "manufacturer": "Example Manufacturer",
  "isActive": true,
  "price": 49.99,
  "quantity": 20
}


Przykładowy request do utworzenia zamówienia,
wcześniej należy dodać przedmioty do bazy.

POST ROUTE: http://localhost/order/create

{
  "customerName": "John Doe",
  "customerEmail": "john.doe@example.com",
  "street": "Main Street",
  "houseNumber": "123",
  "apartmentNumber": "456",
  "postalCode": "12345",
  "city": "Example City",
   "orderItems": [
    {"productId": 3, "quantity": 2},
    {"productId": 4, "quantity": 1}
  ]
}

GET ROUTE: http://localhost/order/serach/{id_zamówienia}
