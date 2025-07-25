<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | Online Grocery Store</title>
  <meta name="description" content="Register to order fresh groceries online. Enjoy live validation and secure account creation.">
  <meta name="keywords" content="register, online grocery, secure, live validation">
  <link rel="canonical" href="http://www.yourdomain.com/register.php">
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Load React and ReactDOM -->
  <script crossorigin src="https://unpkg.com/react@17/umd/react.development.js"></script>
  <script crossorigin src="https://unpkg.com/react-dom@17/umd/react-dom.development.js"></script>
  <!-- Babel for JSX conversion -->
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
  <style>
    .fade-in { animation: fadeIn 1s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
  </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

  <header class="bg-green-700">
    <div class="container mx-auto px-4 py-4 text-white text-center">
      <h1 class="text-3xl font-bold">Register</h1>
    </div>
  </header>

  <!-- Registration Form -->
  <main class="container mx-auto px-4 py-8 flex-grow">
    <div id="register-root" class="max-w-lg mx-auto"></div>
  </main>

  <footer class="bg-green-700">
    <div class="container mx-auto px-4 py-4 text-center text-white">
      &copy; <?php echo date("Y"); ?> Online Grocery Store.
    </div>
  </footer>
  
  <!-- React for registration form -->
  <script type="text/babel">
    class RegisterForm extends React.Component {
      constructor(props) {
        super(props);
        // Initialize state with empty values and an errors object
        this.state = { name: '', phone: '', email: '', password: '', errors: {} };
      }
      
      // Validate a field value and return an error message if invalid
      validate = (field, value) => {
        let error = '';
        if(field === 'name' && !/^[A-Za-z\s]+$/.test(value)){
          error = 'Name must contain only letters and spaces';
        }
        if(field === 'phone' && !/^\d{10}$/.test(value)){
          error = 'Phone must be exactly 10 digits';
        }
        if(field === 'email' && !/^\S+@\S+\.\S+$/.test(value)){
          error = 'Invalid email format';
        }
        if(field === 'password' && value.length < 6){
          error = 'Password must be at least 6 characters';
        }
        return error;
      }
      
      // Handle input field changes and update state
      handleChange = (e) => {
        const { name, value } = e.target;
        const error = this.validate(name, value);
        this.setState(prevState => ({
          [name]: value,
          errors: { ...prevState.errors, [name]: error }
        }));
      }
      
      // Handle form submission
      handleSubmit = (e) => {
        e.preventDefault();
        const { name, phone, email, password, errors } = this.state;
        if(name && phone && email && password && !Object.values(errors).some(err => err !== '')){
          // Send the registration data as JSON to register_action.php
          fetch('register_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, phone, email, password })
          })
          .then(response => response.json())
          .then(data => {
            if(data.success){
              alert("Registration successful. Redirecting to login page...");
              window.location.href = "login.php";
            } else {
              alert("Registration failed: " + data.message);
            }
          });
        } else {
          alert("Please fix errors in the form.");
        }
      }
      
      render() {
        const { errors } = this.state;
        return (
          <div className="bg-white p-8 rounded shadow-md fade-in">
            <h2 className="text-3xl font-semibold mb-6 text-center text-green-700">Create Your Account</h2>
            <form onSubmit={this.handleSubmit}>
              <div className="mb-4">
                <label className="block text-gray-700 mb-2">Name:</label>
                <input type="text" name="name" onChange={this.handleChange} required className="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500" />
                {errors.name && <span className="text-red-500 text-sm">{errors.name}</span>}
              </div>
              <div className="mb-4">
                <label className="block text-gray-700 mb-2">Phone:</label>
                <input type="text" name="phone" onChange={this.handleChange} required className="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500" />
                {errors.phone && <span className="text-red-500 text-sm">{errors.phone}</span>}
              </div>
              <div className="mb-4">
                <label className="block text-gray-700 mb-2">Email:</label>
                <input type="email" name="email" onChange={this.handleChange} required className="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500" />
                {errors.email && <span className="text-red-500 text-sm">{errors.email}</span>}
              </div>
              <div className="mb-4">
                <label className="block text-gray-700 mb-2">Password:</label>
                <input type="password" name="password" onChange={this.handleChange} required className="w-full border border-gray-300 p-2 rounded focus:outline-none focus:ring-2 focus:ring-green-500" />
                {errors.password && <span className="text-red-500 text-sm">{errors.password}</span>}
              </div>
              <button type="submit" className="w-full bg-green-700 text-white p-2 rounded hover:bg-green-800 transition duration-300">Register</button>
            </form>
          </div>
        );
      }
    }
    
    // Render the registration form into the page
    ReactDOM.render(<RegisterForm />, document.getElementById('register-root'));
  </script>
</body>
</html>
