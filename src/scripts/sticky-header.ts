window.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector<HTMLElement>('.site-header')
  const topContent = document.querySelector<HTMLElement>('.top-header-content')
  if (!header) return

  const threshold = 250
  const buffer = topContent?.offsetHeight ? topContent?.offsetHeight : 50
  let isScrolled = false
  let timeoutCall: number | null = null;

  const onScroll = () => {
    const y = window.scrollY

    // Of course, because we are hiding "top-header-content", the result is a fantastical flicker
    // without a buffer equal to the height of the element we are removing.
    const scrolled = isScrolled
      ? y > threshold - buffer
      : y > threshold + buffer

    // Check if scrolled, and toggle adding the relevant classes accordingly
    if (scrolled !== isScrolled) {
      isScrolled = scrolled
      header.classList.toggle('scrolled', scrolled)
      if (topContent) {
        // The timeout is needed to give room for the animation, but we only ever add one at a time (Note the timeoutCall)
        if (!timeoutCall && topContent) {
          timeoutCall = setTimeout(() => {
            topContent.classList.toggle('hidden', scrolled)
            timeoutCall = null
          }, 200)
        }
      }
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true })
})
