import type { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'

export default class Active {
  public async handle({ auth, response }: HttpContextContract, next: () => Promise<void>) {
    if (!auth.user?.active) {
      return response.badRequest({ message: 'Usuario no verificado' })
    }
    await next()
  }
}
