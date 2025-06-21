<script>
  import events from '../../events.json';
  let epochs = Array.from(new Set(events.map(e => e.epoch)));
  let activeEpoch = epochs[0];
  function scrollToId(id) {
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
  }
</script>

<style>
  .timeline-container {
    background: var(--epic-alabaster-bg);
    color: var(--color-piedra-clara, #EAE0C8);
    padding: 1rem;
  }
  .epoch-button {
    background: var(--epic-purple-emperor);
    color: var(--epic-gold-main);
    margin-right: 0.5rem;
    padding: 0.25rem 0.5rem;
    border: none;
    cursor: pointer;
    transition: transform 0.3s ease;
  }
  .epoch-button.active {
    transform: scale(1.1);
  }
  .event-title {
    font-weight: bold;
    background: linear-gradient(90deg, var(--epic-gold-main), var(--epic-purple-emperor));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .event-item {
    margin-bottom: 1rem;
  }
</style>

<div class="timeline-container">
  <div>
    {#each epochs as epoch}
      <button
        class="epoch-button {activeEpoch === epoch ? 'active' : ''}"
        on:click={() => { activeEpoch = epoch; scrollToId(epoch); }}>
        {epoch}
      </button>
    {/each}
  </div>

  {#each events.filter(e => e.epoch === activeEpoch) as event}
    <div id={event.id} class="event-item">
      <h2 class="event-title">{event.date} – {event.title}</h2>
      <p>{event.description}</p>
    </div>
  {/each}
</div>
