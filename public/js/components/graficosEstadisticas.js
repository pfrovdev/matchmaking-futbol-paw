document.addEventListener("DOMContentLoaded", () => {
  const goalsCanvas = document.getElementById("goalsChart");
  
  const canvas = document.getElementById("resultsChart");
  if (!canvas) return;
  
  const ctx = canvas.getContext("2d");

  const data = {
    wins: parseInt(canvas.dataset.wins, 10),
    draws: parseInt(canvas.dataset.draws, 10),
    losses: parseInt(canvas.dataset.losses, 10)
  };

  const colors = ["#4CAF50", "#f0e9e9ff", "#F44336"];
  const total = data.wins + data.draws + data.losses;

  let startAngle = 0;
  Object.values(data).forEach((value, i) => {
    const sliceAngle = (value / total) * 2 * Math.PI;

    ctx.beginPath();
    ctx.moveTo(125, 125);
    ctx.arc(125, 125, 120, startAngle, startAngle + sliceAngle);
    ctx.closePath();
    ctx.fillStyle = colors[i];
    ctx.fill();

    const midAngle = startAngle + sliceAngle / 2;
    const textX = 125 + Math.cos(midAngle) * 80;
    const textY = 125 + Math.sin(midAngle) * 80;

    ctx.fillStyle = "#000";
    ctx.font = "14px Arial";
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText(value, textX, textY);

    startAngle += sliceAngle;
  });

  const labels = ["Victorias", "Empates", "Derrotas"];
  labels.forEach((label, i) => {
    ctx.fillStyle = colors[i];
    ctx.fillRect(260, 20 + i * 20, 12, 12);
    ctx.fillStyle = "#000";
    ctx.font = "12px Arial";
    ctx.textAlign = "left";
    ctx.fillText(label, 280, 30 + i * 20);
  });


  
  if (goalsCanvas) {
    const ctx = goalsCanvas.getContext("2d");

    const goles = parseInt(goalsCanvas.dataset.goles, 10);
    const golesContra = parseInt(goalsCanvas.dataset.golescontra, 10);

    const barWidth = 60;
    const barSpacing = 100;
    const xStart = 80;
    const maxVal = Math.max(goles, golesContra) || 1;
    const chartHeight = 200;

    ctx.fillStyle = "#f5f5f5";
    ctx.fillRect(0, 0, goalsCanvas.width, goalsCanvas.height);

    const scale = chartHeight / maxVal;

    ctx.fillStyle = "#4CAF50";
    ctx.fillRect(xStart, chartHeight - goles * scale, barWidth, goles * scale);
    ctx.fillStyle = "#000";
    ctx.fillText("A favor", xStart, chartHeight + 20);

    if (goles * scale > 20) {
        ctx.fillStyle = "#fff";
        ctx.fillText(goles, xStart + 15, chartHeight - goles * scale + 15);
    } else {
        ctx.fillStyle = "#000";
        ctx.fillText(goles, xStart + 15, chartHeight - goles * scale - 5);
    }

    ctx.fillStyle = "#F44336";
    ctx.fillRect(xStart + barSpacing, chartHeight - golesContra * scale, barWidth, golesContra * scale);
    ctx.fillStyle = "#000";
    ctx.fillText("En contra", xStart + barSpacing, chartHeight + 20);

    if (golesContra * scale > 20) {
        ctx.fillStyle = "#fff";
        ctx.fillText(golesContra, xStart + barSpacing + 10, chartHeight - golesContra * scale + 15);
    } else {
        ctx.fillStyle = "#000";
        ctx.fillText(golesContra, xStart + barSpacing + 10, chartHeight - golesContra * scale - 5);
    }
  }
});
