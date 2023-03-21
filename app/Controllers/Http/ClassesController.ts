import type { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import { schema } from '@ioc:Adonis/Core/Validator'
import Classe from '../../Models/Classe'
import { Model } from '../../interfaces/model'
import Route from '@ioc:Adonis/Core/Route'
// import Ws from '../../Services/Ws'
import Event from '@ioc:Adonis/Core/Event'
// import Env from '@ioc:Adonis/Core/Env'
export default class ClassesController {
  public async index({ response }: HttpContextContract) {
    const classes = await Classe.all()
    const models: Model[] = classes.map((classe) => ({
      id: classe.id,
      attributes: {
        name: classe.name,
      },
      actions: {
        edit_url: Route.makeSignedUrl('editClass', { id: classe.id }),
        delete_url: Route.makeSignedUrl('deleteClass', { id: classe.id }),
      },
    }))
    return response.ok(models)
  }

  public async show() {
    return Classe.query().select('id', 'name')
  }

  public async store({ request, response }: HttpContextContract) {
    const classeSchema = schema.create({
      name: schema.string(),
    })
    const payload = await request.validate({ schema: classeSchema })
    //here i should emit the event
    const classe = await Classe.create(payload)

    Event.emit('new_class', classe.name)

    return response.created({ message: 'Clase creada satisfactoriamente' })
  }

  public async update({ request, response, params }: HttpContextContract) {
    const classeSchema = schema.create({
      name: schema.string(),
    })
    const payload = await request.validate({ schema: classeSchema })

    const classe = await Classe.findOrFail(params.id)

    classe.merge(payload)
    await classe.save()

    return response.created({ message: 'Clase actualizada satisfactoriamente' })
  }
  public async destroy({ response, params }: HttpContextContract) {
    const classe = await Classe.findOrFail(params.id)
    await classe.delete()
    Event.emit('delete_class', classe.name)
    return response.ok({ message: 'Clase eliminada satisfactoriamente' })
  }

  public async emitEvent({ response }: HttpContextContract) {
    const stream = response.response
    stream.writeHead(200,{
      'Content-Type': 'text/event-stream',
      'Cache-Control': 'no-cache',
      'Connection': 'keep-alive',
      'Access-Control-Allow-Origin': '*',
    })
    Event.on('new_class', (classe) => {
      
      stream.write(`event: new_class\ndata: ${classe}\n\n`)
    })
    Event.on('delete_class', (classe) => {
      
      stream.write(`event: delete_class\ndata: ${classe}\n\n`)
    })
  }
}
