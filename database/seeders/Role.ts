import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import Role from '../../app/Models/Role'

export default class extends BaseSeeder {
  public async run() {
    // Write your database queries inside the run method
    await Role.createMany([
      {
        name: 'admin',
      },
      {
        name: 'employee',
      },
      {
        name: 'user',
      },
    ])
  }
}
