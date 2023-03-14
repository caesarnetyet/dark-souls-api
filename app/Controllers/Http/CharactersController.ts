import type { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import { Model } from '../../interfaces/model'
import Character from '../../Models/Character'
import { schema } from '@ioc:Adonis/Core/Validator'
import Route from '@ioc:Adonis/Core/Route'
import Ws from '../../Services/Ws'

export default class CharactersController {
  public async index({ response }: HttpContextContract) {
    const characters = await Character.query().preload('classe')

    const models: Model[] = characters.map((character) => ({
      id: character.id,
      attributes: {
        name: character.name,
        class: character.classe.name,
      },
      actions: {
        edit_url: Route.makeSignedUrl('editCharacter', { id: character.id }),
        delete_url: Route.makeSignedUrl('deleteCharacter', { id: character.id }),
      },
    }))
    return response.ok(models)
  }

  public async store({ request, response }: HttpContextContract) {
    const characterSchema = schema.create({
      name: schema.string(),
      class_id: schema.number(),
    })
    const payload = await request.validate({ schema: characterSchema })

    await Character.create(payload)

    Ws.io.emit('updateCharacter', payload)
    return response.created({ message: 'Personaje creado satisfactoriamente' })
  }

  public async update({ request, response, params }: HttpContextContract) {
    const characterSchema = schema.create({
      name: schema.string(),
      class: schema.number.optional(),
    })
    const payload = await request.validate({ schema: characterSchema })
    payload['class_id'] = payload['class']
    delete payload['class']

    const character = await Character.findOrFail(params.id)

    character.merge(payload)
    await character.save()

    return response.created({ message: 'Personaje actualizado satisfactoriamente' })
  }

  public async destroy({ response, params }: HttpContextContract) {
    const character = await Character.findOrFail(params.id)
    await character.delete()

    Ws.io.emit('updateCharacter', { ok: 'ok' })
    return response.ok({ message: 'Personaje eliminado satisfactoriamente' })
  }
}
