const poll = (fn, interval = 2000) => {
  let timer = null
  const start = () => (timer = setInterval(fn, interval))
  const stop = () => timer && clearInterval(timer)
  return { start, stop }
}
