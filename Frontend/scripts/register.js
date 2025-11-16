
const emailInput = document.getElementById('emailInput');
const passInput = document.getElementById('passwordInput');
const loginBtn = document.getElementById('loginButton');

loginBtn.addEventListener('click', async()=>{

    try{
        const response = await axios.post('../backend/public/register.php',
            {
                email: emailInput.value,
                password: passInput.value,
            },
            {
                headers: { 'Content-Type': 'application/json'},
            }
        );
        console.log("user created successfully");
        window.location.href = 'login.html';
    }catch(error){
        console.error(error);
    }

});
