// alert(bloghost);

$(document).ready(function () {
  const strConsole = $("#mz-bs-pre").html();
  if (strConsole.indexOf(bloghost) === -1) {
    $("#mz-bs-pre").html(strConsole.replace(/\/\/localhost:3000\//g, bloghost));
  }
  const curUrl = window.location.href;
  const orgiUrl = curUrl.replace(`${origin}/`, bloghost);
  $(".mz-bs-orgi").html(`<a href="${orgiUrl}" target="_blank">${orgiUrl}</a>`);
});
