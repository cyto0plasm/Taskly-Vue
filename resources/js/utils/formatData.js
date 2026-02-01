export function formatDate(date) {
  if (!date) return 'No due date';
  const d = new Date(date);
  return d.toLocaleDateString(undefined, {
    weekday: 'short', // Mon, Tue
    year: 'numeric',
    month: 'short', // Jan, Feb
    day: 'numeric'
  });
}
