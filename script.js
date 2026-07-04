const table = document.getElementById("userTable");
const form = document.getElementById("userForm");
const userId = document.getElementById("userId");
const nameInput = document.getElementById("name");
const emailInput = document.getElementById("email");
const ageInput = document.getElementById("age");
const search = document.getElementById("search");
const totalUsers = document.getElementById("totalUsers");

let users = [];

loadUsers();
async function loadUsers() {
    const response = await fetch("api.php");
    users = await response.json();
    displayUsers(users);
}

function displayUsers(data) {
    table.innerHTML = "";
    totalUsers.innerHTML = data.length;
    data.forEach(user => {
        table.innerHTML += `
        <tr>
            <td>${user.id}</td>
            <td>${user.name}</td>
            <td>${user.email}</td>
            <td>${user.age}</td>
            <td>
                <button class="edit" onclick="editUser(${user.id})">Edit</button>
                <button class="delete" onclick="deleteUser(${user.id})">Delete</button>
            </td>
        </tr>
        `;
    });
}
form.addEventListener("submit", async function(e){
    e.preventDefault();
    const user = {

        name: nameInput.value,
        email: emailInput.value,
        age: ageInput.value

    };

    if(userId.value==""){
        await fetch("api.php",{
            method:"POST",
            headers:{
                "Content-Type":"application/json"
            },
            body:JSON.stringify(user)
        });
        showToast("User Added");

    }else{

        await fetch("api.php?id="+userId.value,{
            method:"PUT",
            headers:{
                "Content-Type":"application/json"
            },
            body:JSON.stringify(user)
        });
        showToast("User Updated");
    }
    form.reset();
    userId.value="";
    loadUsers();
});

function editUser(id){

    const user = users.find(u => u.id == id);
    userId.value = user.id;
    nameInput.value = user.name;
    emailInput.value = user.email;
    ageInput.value = user.age;
    window.scrollTo({
        top:0,
        behavior:"smooth"
    });
}

async function deleteUser(id){

    if(confirm("Delete User?")){
        await fetch("api.php?id="+id,{
            method:"DELETE"
        });
        showToast("User Deleted");
        loadUsers();
    }
}

search.addEventListener("keyup",function(){

    const value = search.value.toLowerCase();
    const filtered = users.filter(user =>
        user.name.toLowerCase().includes(value) ||
        user.email.toLowerCase().includes(value)
    );
    displayUsers(filtered);
});

function showToast(message){

    const toast=document.createElement("div");
    toast.className="toast";
    toast.innerHTML=message;
    document.body.appendChild(toast);
    setTimeout(()=>{
        toast.remove();
    },2000);
}