const fs = require('fs');
const path = require('path');

const dataPath = path.join(__dirname, '../frontend/astro-app/events.json');
try {
  const raw = fs.readFileSync(dataPath, 'utf8');
  const events = JSON.parse(raw);
  if (!Array.isArray(events) || events.length === 0) {
    throw new Error('Event array is empty');
  }
  for (const event of events) {
    if (!event.title || !event.date || !event.epoch) {
      throw new Error('Missing required event fields');
    }
  }
  console.log('Timeline events loaded correctly');
} catch (err) {
  console.error('Timeline data error:', err.message);
  process.exit(1);
}

