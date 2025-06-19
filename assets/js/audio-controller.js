document.addEventListener('DOMContentLoaded', () => {
  const AudioContext = window.AudioContext || window.webkitAudioContext;
  const audioCtx = new AudioContext();

  const createTrack = (src, loop = false, volume = 0.5) => {
    const element = new Audio(src);
    element.loop = loop;
    element.crossOrigin = 'anonymous';
    const source = audioCtx.createMediaElementSource(element);
    const gain = audioCtx.createGain();
    gain.gain.value = volume;
    source.connect(gain).connect(audioCtx.destination);
    return { element, gain };
  };

  const ambient = createTrack('https://samplelib.com/lib/preview/mp3/sample-15s.mp3', true, 0.4);
  ambient.element.play().catch(() => {/* autoplay restrictions */});

  if (document.body.classList.contains('interactivo')) {
    const secondary = createTrack('https://samplelib.com/lib/preview/mp3/sample-3s.mp3', true, 0.6);
    secondary.element.play().catch(() => {/* autoplay restrictions */});
  }
});
