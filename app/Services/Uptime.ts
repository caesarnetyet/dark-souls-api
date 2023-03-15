const startupTime = Date.now()

const getUptime = () => {
  const currentTime = Date.now()
  const uptimeInSeconds = (currentTime - startupTime) / 1000
  const uptimeInMinutes = Math.floor(uptimeInSeconds / 60)
  return uptimeInMinutes
}

export default getUptime
