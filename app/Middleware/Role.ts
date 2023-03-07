import type { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'
import User from '../Models/User'

export default class Role {
  public async handle(
    { auth, response }: HttpContextContract,
    next: () => Promise<void>,
    roles: string[]
  ) {
    const user = await User.query().where('id', auth.user?.id).preload('role').firstOrFail()
    for (let i = 0; i < roles.length; i++) {
      if (user.role.name === roles[i]) {
        return await next()
      }
    }
    return response.unauthorized({ message: 'No tienes permisos para realizar esta acción' })
  }
}
