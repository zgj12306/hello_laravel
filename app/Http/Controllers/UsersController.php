<?php

namespace App\Http\Controllers;

use App\Models\User;
use foo\bar;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UsersController extends Controller
{
    /**
     * UsersController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index()
    {
        $users = User::paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $result = $this->validate($request,[
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:8'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $this->sendMailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect()->route('home');
    }

    /**
     * @param $user
     */
    protected function sendMailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    /**
     * @param string $token
     * @return RedirectResponse
     */
    public function confirmEmail(string $token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $this->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);

    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return Factory|View
     */
    public function show(User $user)
    {
        $statuses = $user->statuses()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('users.show', compact('user', 'statuses'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     * @return Factory|View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws ValidationException
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', '个人资料更新成功！');
        return redirect()->route('users.show', $user->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return RedirectResponse
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
