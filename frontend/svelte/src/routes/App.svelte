<script lang="ts">
  let open = false
</script>

<div class:menu-compressed={open} class:menu-open-left={open}>
  <header class="header">
    <h1 class="title gradient-text">Condado de Castilla</h1>
    <button class="toggle" aria-expanded={open} on:click={() => (open = !open)}>
      ☰
    </button>
  </header>
  <nav class="slide-menu" class:open={open}>
    <ul>
      <li><a href="#inicio">Inicio</a></li>
      <li><a href="#historia">Historia</a></li>
      <li><a href="#arqueologia">Arqueología</a></li>
      <li><a href="#foro">Foro</a></li>
    </ul>
  </nav>
  <main id="inicio">
    <section class="hero">
      <h2 class="gradient-text">Cuna de la Cultura Hispana</h2>
      <p>Promocionamos el turismo en Cerezo de Río Tirón y gestionamos su patrimonio arqueológico y cultural.</p>
    </section>
  </main>
</div>

<style>
  @import '../style.css';
</style>
