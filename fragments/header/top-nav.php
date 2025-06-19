<nav id="top-nav" class="fixed top-0 inset-x-0 z-50 text-yellow-200 bg-transparent transition-colors duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
    <a href="/index.php" class="font-serif text-2xl tracking-wide gradient-text">Condado de Castilla</a>
    <button id="mobile-menu-btn" aria-label="Abrir men\u00fa" aria-expanded="false" tabindex="0" class="md:hidden focus:outline-none">
      <svg class="h-6 w-6 text-yellow-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    <ul class="hidden md:flex space-x-6 font-serif text-lg tracking-wide">
      <li><a href="/index.php" class="nav-link hover:text-yellow-300 transition-colors duration-300">Inicio</a></li>
      <li><a href="/historia/historia.php" class="nav-link hover:text-yellow-300 transition-colors duration-300">Historia</a></li>
      <li><a href="/museo/galeria.php" class="nav-link hover:text-yellow-300 transition-colors duration-300">Yacimientos</a></li>
      <li><a href="/contacto/contacto.php" class="nav-link hover:text-yellow-300 transition-colors duration-300">Contacto</a></li>
    </ul>
  </div>
</nav>
<div id="mobile-menu" tabindex="-1" class="fixed inset-0 bg-purple-900 bg-opacity-95 backdrop-blur hidden flex-col items-center justify-center space-y-8">
  <a href="/index.php" class="mobile-link text-2xl text-yellow-300 font-serif transform -translate-y-5 opacity-0">Inicio</a>
  <a href="/historia/historia.php" class="mobile-link text-2xl text-yellow-300 font-serif transform -translate-y-5 opacity-0">Historia</a>
  <a href="/museo/galeria.php" class="mobile-link text-2xl text-yellow-300 font-serif transform -translate-y-5 opacity-0">Yacimientos</a>
  <a href="/contacto/contacto.php" class="mobile-link text-2xl text-yellow-300 font-serif transform -translate-y-5 opacity-0">Contacto</a>
</div>
