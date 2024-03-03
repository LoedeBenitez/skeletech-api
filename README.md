**SKELETECH API**

---------------------------------

#Getting Started
1. Install Laragon Wamp Server
2. Once installed, clone repository and locate the "C:/laragon/www" as the path
2. Press "Start All"
3. Open Terminal in Laragon
  3.1 Run cd skeletech-api
  3.2 Run composer install
  3.3 Locate the ".env example" file and duplicate it while removing the "example" extension in the .env file
  3.4 Run php artisan key:generate
  3.5 Run npm install && npm run build
  3.6 Run php artisan migrate --seed

Running the API Server
0. You can view all the routes in the "routes/api.php" file
1. Switch to "Loede-Dev" branch
2. Through POSTMAN, type the API URL "http://skeletech-api.test/api/{routes}"

----------------------------------

#Getting Familliar with Routes without Bearer Token
1. POST Auth Login ("http://skeletech-api.test/api/login") 
  1.1 The fields are: 
       'email' => 'required',
       'password' => 'required|min:6'
  Token will be created once created, it will be in the response as "success".

2. POST Registering Users ("http://skeletech-api.test/api/personal/information/create")
  2.1 The fields are: 
        'email' => 'required|email|unique:credentials,email',
        'password' => 'required|min:6',
        'password_confirmation' => 'required|min:6',
        'prefix' => 'nullable|string|max:10',
        'first_name' => 'required|string|max:25',
        'middle_name' => 'required|string|max:25',
        'last_name' => 'required|string|max:25',
        'suffix' => 'nullable|string|max:10',
        'alias' => 'nullable|string|max:15',
        'gender' => 'required|string|max:10',
        'birth_date' => 'required|date|date_format:Y-m-d',
        'age' => 'required|integer'
  It will automatically create the credential of the user as well as the personal information of the user.

-------------------------------------

#Getting Familliar with Routes with Bearer Token
0. Include the sent Bearer Token by the Login API in your HTTP request
  0.1 React Axios Call
     const response = await axios.get('http://example.com/api/data', {
        headers: {
            'Authorization': 'Bearer YOUR_TOKEN_HERE'
        }
    });
1. GET Auth Logout ("http://skeletech-api.test/api/login")
  1.1 The Fields are: None
  It will destroy all the tokens created by this user

2. POST Auth Change Password ("http://skeletech-api.test/api/change/password")
  2.1 The Fields are: 
       'old_password' => 'required',
       'new_password' => 'required|min:6|different:old_password',
       'new_password_confirmation' => 'required|min:6'

3. POST Auth Change Status ("http://skeletech-api.test/api/change/status")
  3.1 The Fields are: None
  It will suspend the account of the user, unabling the action of the user to login

4. GET Personal Information Get By ID ("http://skeletech-api.test/api/personal/information/get/{id}")
  4.1 Input the actual ID of the user in the "{id}"

5. GET Personal Information Get All ("http://skeletech-api.test/api/personal/information/all") 

6. POST Personal Information Paginated List ("http://skeletech-api.test/api/personal/information/get") 
  6.1 Fields that can be used
       'display' => 'nullable|integer', (How many per page you should display)
       'page' => 'nullable|integer', (What Page are you on)
       'search' => 'nullable|string', (What are the searchable fields)
       'status' => 'nullable|integer', (Filter through status active = 1 and inactive = 0)
        Searchable Fields are stated here: 
            'email',
            'first_name',
            'middle_name',
            'last_name',
            'prefix',
            'suffix',
            'alias',
            'gender',
            'birth_date',
            'age',
  When fields are not present, it will proceed to get all. 
  6.2 Total pages that are returned by the Paginated List can be used as dynamic pagination in the front-end.

7. POST Personal Information Change Status ("http://skeletech-api.test/api/personal/information/status/{id}") 
  7.1 Input the actual ID of the user in the "{id}"

8. POST Credential Delete ("http://skeletech-api.test/api/personal/information/delete/{id}") 
  8.1 Input the actual ID of the user in the "{id}"
  8.2 Deleting the user account means deleting all data (Even the Personal Information)
