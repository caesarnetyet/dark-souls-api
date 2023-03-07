import BaseSeeder from '@ioc:Adonis/Lucid/Seeder'
import Classe from '../../app/Models/Classe'

export default class extends BaseSeeder {
  public async run() {
    // Write your database queries inside the run method
    await Classe.createMany([
      {
        name: 'Warrior',
      },
      {
        name: 'Paladin',
      },
      {
        name: 'Hunter',
      },
    ])
  }
}
