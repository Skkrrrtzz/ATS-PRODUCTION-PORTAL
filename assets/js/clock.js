function updateTime() {
  const now = new Date();

  const options = {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    timeZoneName: "short",
  };

  const formattedDate = now.toLocaleDateString(undefined, options);

  document.getElementById("clock").textContent = formattedDate;
}

updateTime();
setInterval(updateTime, 1000); // Update every second
