import { useState } from 'react'

export default function App() {
  const [open, setOpen] = useState(false)
  return (
    <div className={open ? 'menu-compressed menu-open-left' : ''}>
      <header className="header">
        <h1 className="title gradient-text">Condado de Castilla</h1>
        <button
          className="toggle"
          aria-expanded={open}
          onClick={() => setOpen(o => !o)}
        >
          ☰
        </button>
      </header>
      <nav className={open ? 'slide-menu open' : 'slide-menu'}>
        <ul>
          <li><a href="#inicio">Inicio</a></li>
          <li><a href="#historia">Historia</a></li>
          <li><a href="#arqueologia">Arqueología</a></li>
          <li><a href="#foro">Foro</a></li>
        </ul>
      </nav>
      <main id="inicio">
        <section className="hero">
          <h2 className="gradient-text">Cuna de la Cultura Hispana</h2>
          <p>Promocionamos el turismo en Cerezo de Río Tirón y gestionamos su patrimonio arqueológico y cultural.</p>
        </section>
      </main>
    </div>
  )
}
