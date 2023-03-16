import type { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import { schema } from '@ioc:Adonis/Core/Validator'
import Classe from '../../Models/Classe'
import { Model } from '../../interfaces/model'
import Route from '@ioc:Adonis/Core/Route'
import Ws from '../../Services/Ws'
import Event from '@ioc:Adonis/Core/Event'

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

    Event.emit('new:class', classe.name)

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
    Ws.io.emit('deletedClass', classe.name)
    await classe.delete()
    return response.ok({ message: 'Clase eliminada satisfactoriamente' })
  }

  public async emitEvent({ response }: HttpContextContract) {
    const data = {
      event: 'new_class',
      data: 'hola',
    }

    response.header('Content-Type', 'text/event-stream')
    response.header('Cache-Control', 'no-cache')
    response.header('Connection', 'keep-alive')

    const sendResponse = (name: string) => {
      response.send(`event: ${data.event}\ndata: ${JSON.stringify(name)}\n\n`)
    }

    Event.on('new:class', sendResponse)
  }
}
