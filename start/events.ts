import Event from '@ioc:Adonis/Core/Event'

/*
|--------------------------------------------------------------------------
| Preloaded File
|--------------------------------------------------------------------------
|
| Any code written inside this file will be executed during the application
| boot.
|
*/
Event.on('new:class', 'Classe.onNewClasse')
