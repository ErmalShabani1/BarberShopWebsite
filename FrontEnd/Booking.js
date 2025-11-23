const users = [
    { username: "admin" , password:"admin123", role:"admin"},
    {username:"barber", paassword:"barber123", role:"barber"},
    {username:"user", password:"user123", role:"user"},
];

const loginForm = document.getElemenetById("loginForm");
const loginContainer = document.getElementById("loginContainer");
const dashboard = document.getElementById("dashborad");
const userRoleSpan = document.getElementById("userRole");

const adminSection = document.getElementById("adminSection");
const barberSection = document.getElementById("barberSection");
const costumerSection = document.getElementById("costumerSection");

loginForm.addEventListener("submit", function(e){
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();
    const role = document.getElementById("role").value;

    if(username === "" || password === "" || role === ""){
        alert("please fill in all fields");
        return;
    }

    const user = users.find(u => u.username === username && u.password === password && u.role === role);

    
});

function showDashboard(role){
    loginContainer.style.display = "none";
    dashboard.style.display = "block";
    userRoleSpan.innerText = role;

    adminSection.style.display = role === "admin" ? "block" : "none";
    barberSection.style.display = role === "barber" ? "block" : "none";
    costumerSection.style.display = role === "customer" ? "block" : "none";
}

document.getElementById("logoutBtn").addEventListener("click",function(){
    dashboard.style.display = "none";
    loginContainer.style.display = "block";
});

