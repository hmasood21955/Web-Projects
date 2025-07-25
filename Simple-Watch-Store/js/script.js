function validate(event) {
  let isValid = true;   

  if (!document.getElementById('name').value) {
      
      document.getElementById('nameError').textContent = 'Name is required.';
      isValid = false; 
  } else {

      document.getElementById('nameError').textContent = '';
  }


  let email = document.getElementById('email').value; 
  let emailPattern = /^[^@]+@[^@]+\.[a-z]{2,6}$/i; 


  if (!emailPattern.test(email)) {
      document.getElementById('emailError').textContent = 'Enter a valid email.';
      isValid = false;
  } else {
      
      document.getElementById('emailError').textContent = '';
  }

  if (!isValid) {
      event.preventDefault(); 
  }
}

document.getElementById('contactForm').addEventListener('submit', validate);
