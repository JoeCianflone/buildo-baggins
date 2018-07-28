# Buildo Baggins

Does a build thingy

[https://dailylolpics.com/wp-content/uploads/2017/09/Dat-fro-do-meme.jpg]

## Usage

**This should be used locally.**

Pull down this repo and run `composer install`

You're probably going to have to give execute permissions to the file to run so `cd` into the folder and run:

```bash
chmod +X buildo
```

Now you run the commands like this:

```bash
$ php buildo compile <domain> <path> <local-name>
```

So you want to pull down the index page running on your own server at `http://foo.test` that would look like this:

```bash
$ php buildo compile http://foo.test / index.htm
```

The file will be saved locally to a folder called `latest`
