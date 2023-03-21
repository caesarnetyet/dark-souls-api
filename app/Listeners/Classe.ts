import type { EventsList } from '@ioc:Adonis/Core/Event'
import Env from '@ioc:Adonis/Core/Env'

export default class Classe {
  public async onNewClasse(classe: EventsList['new:class']) {
    console.log('classeListener:' + classe)
    Env.set('CLASSE', classe)
  }
}
