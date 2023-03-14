import type { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import Hash from '@ioc:Adonis/Core/Hash'
import { schema, rules } from '@ioc:Adonis/Core/Validator'
import Env from '@ioc:Adonis/Core/Env'
import { Queue } from 'bullmq'
import User from '../../Models/User'
import Route from '@ioc:Adonis/Core/Route'
import { Model } from '../../interfaces/model'
import Role from '../../Models/Role'
export default class UsersController {
  public async index({ response }: HttpContextContract) {
    const users = await User.query().preload('role')
    const models: Model[] = users.map((user) => ({
      id: user.id,
      attributes: {
        name: user.name,
        email: user.email,
        phone: user.phone,
        role: user.role.name,
        active: user.active,
      },
      actions: {
        edit_url: Route.makeSignedUrl('editUser', { id: user.id }),
        delete_url: Route.makeSignedUrl('deleteUser', { id: user.id }),
      },
    }))
    return response.ok(models)
  }

  public async store({ request, response }: HttpContextContract) {
    const userSchema = schema.create({
      name: schema.string(),
      email: schema.string({}, [rules.email(), rules.unique({ table: 'users', column: 'email' })]),
      password: schema.string({}, [rules.minLength(6)]),
      phone: schema.string({}, [rules.minLength(10)]),
    })

    const payload = await request.validate({ schema: userSchema })
    payload['password'] = await Hash.make(payload['password'])
    payload['role_id'] = 3
    const user = await User.create(payload)

    const signedUrl = Route.makeSignedUrl(
      'verify',
      { id: user.id },
      { expiresIn: '1 day', prefixUrl: Env.get('BASE_URL') }
    )

    const url = Env.get('FRONTEND_URL') + '/verify?url=' + signedUrl
    const queue = new Queue('emails')
    await queue.add('sendEmail', { email: user.email, url: url })

    return response.created({ message: 'Usuario Creado Satisfactoriamente' })
  }

  public async verify({ params, response }: HttpContextContract) {
    const user = await User.findOrFail(params.id)
    if (user.active) {
      return response.badRequest({ message: 'Usuario ya verificado' })
    }
    const code = Math.floor(Math.random() * 8999) + 1000
    user.code = code.toString()
    await user.save()
    const queue = new Queue('sms')
    await queue.add('sendSms', { phone: user.phone, code: code })
    const url = Route.makeSignedUrl(
      'verifyCode',
      { id: user.id },
      { expiresIn: '1 day', prefixUrl: Env.get('BASE_URL') }
    )
    return response.ok({ message: 'Revisa el codigo enviado a tu celular!', url: url })
  }

  public async verifyCode({ params, request, response }: HttpContextContract) {
    const user = await User.findOrFail(params.id)
    const codeSchema = schema.create({
      code: schema.string({}, [rules.minLength(4), rules.maxLength(4)]),
    })
    const payload = await request.validate({ schema: codeSchema })
    if (user.code !== payload['code']) {
      return response.badRequest({ message: 'Codigo incorrecto' })
    }
    user.active = true
    await user.save()
    return response.ok({ message: 'Usuario verificado satisfactoriamente' })
  }

  public async login({ request, response, auth }: HttpContextContract) {
    const userSchema = schema.create({
      email: schema.string({}, [rules.email(), rules.exists({ table: 'users', column: 'email' })]),
      password: schema.string({}, [rules.minLength(6)]),
    })
    const payload = await request.validate({ schema: userSchema })
    const user = await User.query().where('email', payload['email']).firstOrFail()

    if (!user.active) {
      return response.status(401).send({ errors: [{ message: 'Usuario no activo' }] })
    }
    const token = await auth.use('api').attempt(payload['email'], payload['password'])
    return response.ok({ token })
  }

  public async getUser({ auth, response }: HttpContextContract) {
    const user = await User.query().preload('role').where('id', auth.user?.id).firstOrFail()

    const model: Model = {
      id: user.id,
      attributes: {
        name: user.name,
        email: user.email,
        phone: user.phone,
        role: user.role.name,
        active: user.active,
      },
      actions: {
        edit_url: Route.makeSignedUrl('editUser', { id: user.id }),
        delete_url: Route.makeSignedUrl('deleteUser', { id: user.id }),
      },
    }
    return response.ok(model)
  }

  public async editUser({ request, response, params }: HttpContextContract) {
    const userSchema = schema.create({
      name: schema.string.optional(),
      email: schema.string.optional({}, [rules.email()]),
      password: schema.string.optional({}, [rules.minLength(6)]),
      phone: schema.string.optional({}, [rules.minLength(10)]),
      role: schema.number.optional(),
      active: schema.boolean.optional(),
    })

    const payload = await request.validate({ schema: userSchema })
    payload['role_id'] = payload['role']
    delete payload['role']
    const user = await User.findOrFail(params.id)
    user.merge(payload)
    await user.save()
    return response.ok({ message: 'Usuario Actualizado Satisfactoriamente' })
  }

  public async deleteUser({ response, params }: HttpContextContract) {
    const user = await User.findOrFail(params.id)
    await user.delete()
    return response.ok({ message: 'Usuario Eliminado Satisfactoriamente' })
  }

  public async getRoles() {
    return Role.query().select('id', 'name')
  }
}
