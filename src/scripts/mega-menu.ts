window.addEventListener('DOMContentLoaded', () => {
  const menuItems = document.querySelectorAll<HTMLElement>(
    '.menu-item-has-children'
  )

  menuItems.forEach(item => {
    let closeTimeout: number | null = null

    item.addEventListener('mouseenter', () => {
      if (closeTimeout) {
        clearTimeout(closeTimeout)
        closeTimeout = null
      }
      item.classList.add('petland-menu-open')
    })

    item.addEventListener('mouseleave', () => {
      closeTimeout = window.setTimeout(() => {
        item.classList.remove('petland-menu-open')
        closeTimeout = null
      }, 100) // delay in ms
    })
  })
  // The mobile stuff:
  document.querySelectorAll('.submenu-toggle').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault()
      const parent = btn.closest('.menu-item-has-children')
      const submenu = parent?.querySelector(
        ':scope > .sub-menu'
      ) as HTMLElement | null
      const anchor = parent?.querySelector(':scope > a') as HTMLElement | null
      if (!submenu || !anchor) return
      submenu.classList.toggle('open')
      anchor.classList.toggle('open')
      btn.setAttribute(
        'aria-expanded',
        submenu.classList.contains('open') ? 'true' : 'false'
      )
    })
  })
  // Toggle mobile menu
  const burger = document.querySelector('.nav-burger')
  const closeBtn = document.querySelector('.nav-close-btn')
  const nav = document.querySelector('.petland-navigation')

  burger?.addEventListener('click', () => {
    burger?.setAttribute('aria-expanded', 'true')
    nav?.classList.add('open')
    document.body.classList.toggle('menu-open')
  })
  closeBtn?.addEventListener('click', () => {
    burger?.setAttribute('aria-expanded', 'false')
    nav?.classList.remove('open')
    document.body.classList.toggle('menu-open')
  })

  // Remove class if the browser is resized to avoid problems with the desktop menus
  window.addEventListener('resize', () => {
    burger?.setAttribute('aria-expanded', 'false')
    document.body.classList.remove('menu-open')
    nav?.classList.remove('open')
  })
})
