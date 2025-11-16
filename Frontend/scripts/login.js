
const emailInput = document.getElementById('emailInput');
const passInput = document.getElementById('passwordInput');
const loginBtn = document.getElementById('loginButton');

loginBtn.addEventListener('click', async()=>{

    try{
        const response = await axios.post('../backend/public/login.php',
            {
                email: emailInput.value,
                password: passInput.value,
            },
            {
                headers: { 'Content-Type': 'application/json'},
            }
        );

        const userData  = response.data.data;
        localStorage.setItem('id',userData.id);
        localStorage.setItem('token', userData.token);
        localStorage.setItem('role', userData.role);

        window.location.href = 'dashboard.html';
    }catch(error){
        console.error(error);
    }

});
