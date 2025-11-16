
const localtoken = localStorage.getItem("token");
const localrole = localStorage.getItem("role");
if(!localtoken){
    window.location.href = "login.html";
}

const logoutbtn = document.getElementById("logoutButton");

logoutbtn.addEventListener('click', async()=>{

localStorage.removeItem("id");
localStorage.removeItem("token");
localStorage.removeItem("role");

window.location.href = "login.html";
});
const rawText = document.getElementById("rawText");
const submit = document.getElementById("submit");

submit.addEventListener('click', async()=>{

    try{
        const $response = await axios.post("../Backend/public/create_entry.php",
            {
                raw_text: rawText.value,
            },
            {
                headers: {'X-Auth-Token':localStorage.getItem('token')}
            }
        );
    }catch(error){
        console.error(error);
    }

});