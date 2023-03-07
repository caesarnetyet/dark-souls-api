import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import User from '../../app/Models/User'
import Hash from '@ioc:Adonis/Core/Hash'
export default class extends BaseSeeder {
  public async run() {
    // Write your database queries inside the run method
    await User.createMany([
      {
        name: 'admin',
        email: 'admin@gmail.com',
        password: await Hash.make('adminadmin'),
        phone: '1234567890',
        active: true,
        role_id: 1,
      },
      {
        name: 'employee',
        email: 'employee@gmail.com',
        password: await Hash.make('employee'),
        phone: '1234567890',
        active: true,
        role_id: 2,
      },
    ])
  }
}
