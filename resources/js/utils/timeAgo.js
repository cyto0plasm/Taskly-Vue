//converts a date to a "time ago" string (e.g., "5 minutes ago")
export function timeAgo(date) {
  if (!date) return "";

  const createdAt = new Date(date);
  const diff = new Date() - createdAt; // milliseconds

  const units = [
    { name: "year", ms: 1000 * 60 * 60 * 24 * 365 },
    { name: "month", ms: 1000 * 60 * 60 * 24 * 30 },
    { name: "day", ms: 1000 * 60 * 60 * 24 },
    { name: "hour", ms: 1000 * 60 * 60 },
    { name: "minute", ms: 1000 * 60 },
    { name: "second", ms: 1000 },
  ];

  for (const unit of units) {
    const amount = Math.floor(diff / unit.ms);
    if (amount > 0) {
      return `${amount} ${unit.name}${amount > 1 ? "s" : ""} ago`;
    }
  }

  return "just now";
}
