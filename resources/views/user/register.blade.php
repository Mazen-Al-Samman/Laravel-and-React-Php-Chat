<div class="container">
    <h1>Register New User</h1>
    <br>

    <form method="POST" action="/user/store">
        @csrf
        @method('POST')
        <input type="text" class="form-control" placeholder="User Name" name="name">
        <input type="text" class="form-control" placeholder="Email" name="email">
        <input type="password" class="form-control" placeholder="Password" name="password">
        <input type="submit" value="save">
    </form>
</div>
