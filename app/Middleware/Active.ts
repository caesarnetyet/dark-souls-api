import type { HttpContextContract } from '@ioc:Adonis/Core/HttpContext'

export default class Active {
  public async handle({ auth, response }: HttpContextContract, next: () => Promise<void>) {
    if (!auth.user?.active) {
      return response.status(401).send({ message: 'Usuario no activo' })
    }
    await next()
  }
}
