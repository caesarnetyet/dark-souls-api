import { BaseCommand } from '@adonisjs/core/build/standalone'
import Mail from '@ioc:Adonis/Addons/Mail'
import axios from 'axios'
import { Job, Worker } from 'bullmq'
import Env from '@ioc:Adonis/Core/Env'

export default class BullWork extends BaseCommand {
  /**
   * Command name is used to run the command
   */
  public static commandName = 'bull:work'

  /**
   * Command description is displayed in the "help" output
   */
  public static description = ''

  public static settings = {
    /**
     * Set the following value to true, if you want to load the application
     * before running the command. Don't forget to call `node ace generate:manifest`
     * afterwards.
     */
    loadApp: true,

    /**
     * Set the following value to true, if you want this command to keep running until
     * you manually decide to exit the process. Don't forget to call
     * `node ace generate:manifest` afterwards.
     */
    stayAlive: true,
  }

  public async run() {
    const emails = new Worker('emails', async (job: Job) => {
      console.log(`Processing job ${job.id}`, JSON.stringify(job.data))
      const { url, email } = job.data
      await Mail.send((message) => {
        message
          .from('caesarnetyet@gmail.com')
          .to(email)
          .subject('Cuenta creada satisfactoriamente')
          .htmlView('email/register', { url })
      })
    })
    emails.on('completed', (job) => {
      console.log(`Job ${job.id} completed!`)
    })
    emails.on('failed', (job, err) => {
      console.log(`Job ${job!.id} failed with ${err.message}`)
    })

    const sms = new Worker('sms', async (job: Job) => {
      console.log(`Processing job ${job.id}`, JSON.stringify(job.data))
      const { phone, code } = job.data
      const url = `https://api.twilio.com/2010-04-01/Accounts/${Env.get('SID')}/Messages.json`
      const params = new URLSearchParams()
      params.append('To', '+52' + phone)
      params.append('From', Env.get('PHONE'))
      params.append('Body', `Dark souls APP: Tu codigo de verificacion es: ${code}`)
      console.log(params)
      const auth = {
        username: Env.get('SID'),
        password: Env.get('TOKEN'),
      }
      await axios.post(url, params, { auth })
    })

    sms.on('completed', (job) => {
      console.log(`Job ${job.id} completed!`)
    })
    sms.on('failed', (job, err) => {
      console.log(`Job ${job!.id} failed with ${err.message}`)
    })
  }
}
