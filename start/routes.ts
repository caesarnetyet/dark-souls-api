/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
|
| This file is dedicated for defining HTTP routes. A single file is enough
| for majority of projects, however you can define routes in different
| files and just make sure to import them inside this file. For example
|
| Define routes in following two files
| ├── start/routes/cart.ts
| ├── start/routes/customer.ts
|
| and then import them inside `start/routes.ts` as follows
|
| import './routes/cart'
| import './routes/customer'
|
*/

import Route from '@ioc:Adonis/Core/Route'

Route.get('/', async () => {
  return { hello: 'world' }
})
Route.get('/roles', 'UsersController.getRoles').middleware('auth:api')
Route.get('/users', 'UsersController.index').middleware(['auth:api', 'role:admin', 'active'])
Route.group(() => {
  Route.get('/', 'UsersController.getUser').middleware('auth:api')
  Route.post('/', 'UsersController.store')
  Route.post('/login', 'UsersController.login')
  Route.get('/verify/:id', 'UsersController.verify').as('verify')
  Route.post('/verify/:id', 'UsersController.verifyCode').as('verifyCode')
  Route.group(()=> {
    Route.put('/edit/:id', 'UsersController.editUser').as('editUser')
    Route.delete('/delete/:id', 'UsersController.deleteUser').as('deleteUser')
  }).middleware(['auth:api', 'active', 'role:admin,user'])
}).prefix('/user')


Route.group(()=> {
Route.get('/classes', 'ClassesController.index').middleware('role:employee')
Route.group(()=> {
  Route.get('/', 'ClassesController.show')
  Route.group(()=> {
    Route.post('/', 'ClassesController.store')
    Route.put('/update/:id', 'ClassesController.update').as('updateClass')
    Route.delete('/delete/:id', 'ClassesController.destroy').as('deleteClass')
  }).middleware('role:employee')
}).prefix('/class')
}).middleware(['auth:api', 'active'])

Route.group(()=> {
  Route.get('/characters', 'charactersController.index').middleware('role:user')
  Route.group(()=> {
    Route.get('/', 'charactersController.show')
    Route.group(()=> {
      Route.post('/', 'charactersController.store')
      Route.put('/update/:id', 'charactersController.update').as('updateCharacter')
      Route.delete('/delete/:id', 'charactersController.destroy').as('deleteCharacter')
    }).middleware('role:user')
  }).prefix('/character')
  }).middleware(['auth:api', 'active'])