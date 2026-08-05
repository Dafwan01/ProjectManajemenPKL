import "@fontsource-variable/inter"; // Ini otomatis meng-import font Inter lokal
import Chart from "chart.js/auto";
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import "@fortawesome/fontawesome-free/css/all.min.css";
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";
import "@fontsource-variable/outfit";
delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

window.L = L;
window.Chart = Chart;
