import type { EventsList } from '@ioc:Adonis/Core/Event'

export default class Classe {
  public async onNewClasse(classe: EventsList['new:class']) {
    console.log('classeListener:' + classe)
  }
}
