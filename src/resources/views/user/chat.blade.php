@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/chat.css') }}">
@endsection

@section('content')
<div class="chat__content">
    <div class="chat__sidebar">
        <h2 class="chat__sidebar-title">その他の取引</h2>
        <ul class="chat__sidebar-list">
            @php
                $otherTransactionOrders = $transactionOrders->reject(function($tOrder) use ($order) {
                    return $tOrder->id === $order->id;
                });
            @endphp

            @forelse ($otherTransactionOrders as $tOrder)
                <li class="chat__sidebar-item">
                    <a href="{{ route('chats.show', $tOrder->id) }}">
                        {{ $tOrder->item->name ?? '商品名なし' }}
                    </a>
                </li>
            @empty
                <li class="chat__sidebar-item">取引中の商品はありません</li>
            @endforelse
        </ul>
    </div>

    <div class="chat__main">
        <div class="chat__header">
            @php
                $otherUser = auth()->id() === $order->buyer_id ? $order->seller : $order->buyer;
            @endphp
            <div class="chat__header-left">
                <img src="{{ asset('storage/images/profiles/' . ($otherUser->profile->image ?? 'default.png')) }}"  
                     class="chat__user-image" alt="ユーザー画像">
                <h1 class="chat__user-title">
                    <span class="chat__user-name">{{ $otherUser->name ?? 'ユーザー名' }}</span> さんとの取引画面
                </h1>
            </div>
            @if(auth()->id() === $order->buyer_id && $order->status === 'pending')
                <button id="completeBtn" class="chat__complete-btn">取引を完了する</button>
            @endif
        </div>

        <hr class="chat__divider">

        <div class="chat__product">
            <img src="{{ asset('storage/images/items/' . $order->item->image) }}" class="chat__product-image" alt="商品画像">
            <div class="chat__product-info">
                <p class="chat__product-name">{{ $order->item->name }}</p>
                <p class="chat__product-price">¥{{ number_format($order->item->price) }}</p>
            </div>
        </div>

        <hr class="chat__divider">

        @if($chats->isEmpty())
            <p class="chat__no-messages">まだメッセージはありません。</p>
        @else
            <div class="chat__messages">
                @foreach($chats as $chat)
                    @php
                        $isMe = $chat->sender_id === auth()->id();
                    @endphp
                    <div class="chat__message {{ $isMe ? 'chat__message--right' : 'chat__message--left' }}">
                        <div class="chat__message-header">
                            @if(!$isMe)
                                <img src="{{ asset('storage/images/profiles/' . ($chat->sender->profile->image ?? 'default.png')) }}" 
                                     class="chat__message-user-image" alt="ユーザー画像">
                                <span class="chat__message-username">{{ $chat->sender->name }}</span>
                            @else
                                <span class="chat__message-username">{{ auth()->user()->name }}</span>
                                <img src="{{ asset('storage/images/profiles/' . $profile->image) }}"  
                                     class="chat__message-user-image" alt="自分画像">
                            @endif
                        </div>

                        <div class="chat__message-body">{{ $chat->message }}</div>

                        @if($chat->image)
                            <div class="chat__message-image">
                                <img src="{{ asset('storage/' . $chat->image) }}" alt="送信画像">
                            </div>
                        @endif

                        @if ($isMe)
                            <div class="chat__message-actions">
                                <button type="button" class="chat__edit-btn" data-id="{{ $chat->id }}" data-message="{{ $chat->message }}">
                                    編集
                                </button>
                                <form action="{{ route('chats.destroy', $chat->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="chat__delete-btn">削除</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="chat__input-area-wrapper">
            <div class="chat__errors">
                @error('message')
                    <p class="chat__error">{{ $message }}</p>
                @enderror
                @error('image')
                    <p class="chat__error">{{ $message }}</p>
                @enderror
            </div>

            <form action="{{ route('chats.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="chat__input-area">
                @csrf
                <input type="text" name="message" id="chatMessage" class="chat__input" placeholder="取引メッセージを記入してください">
                <input type="file" name="image" id="chatImage" class="chat__image-input" accept="image/*" style="display:none;">
                <button type="button" id="chatImageBtn" class="chat__image-btn">画像を追加</button>
                <button type="submit" class="chat__send-btn">
                    <img src="{{ asset('storage/images/icons/paper-plane.jpg') }}" class="chat__send-icon" alt="送信">
                </button>
            </form>
        </div>

        <div id="editModal" class="chat__edit-modal" style="display:none;">
            <div class="chat__edit-modal-content">
                <form id="editForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <textarea name="message" id="editMessage" class="chat__edit-textarea"></textarea>
                    <div class="chat__edit-buttons">
                        <button type="submit" class="chat__edit-submit">更新</button>
                        <button type="button" class="chat__edit-cancel">キャンセル</button>
                    </div>
                </form>
            </div>
        </div>

        @if(auth()->id() === $order->buyer_id && $order->status === 'pending')
            <div id="completeModal" class="modal" style="display:none;">
                <div class="modal__content">
                    <p class="modal__title">取引が完了しました。</p>
                    <hr class="modal__divider">
                    <p class="modal__subtitle">今回の取引相手はどうでしたか？</p>
                    <form action="{{ route('rating.store', $order->id) }}" method="POST" id="ratingForm">
                        @csrf
                        <input type="hidden" name="score" id="ratingScore" value="0">
                        <div class="modal__stars" id="starRating">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star" data-value="{{ $i }}">★</span>
                            @endfor
                        </div>
                        <hr class="modal__divider">
                        <div class="modal__footer">
                            <button type="submit" class="modal__submit">送信する</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($canRate)
            <div id="sellerCompleteModal" class="modal" style="display:flex;">
                <div class="modal__content">
                    <p class="modal__title">購入者の評価をしてください</p>
                    <form action="{{ route('rating.store', $order->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="score" id="sellerRatingScore" value="0">
                        <div class="modal__stars" id="sellerStarRating">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="star" data-value="{{ $i }}">★</span>
                            @endfor
                        </div>
                        <hr class="modal__divider">
                        <div class="modal__footer">
                            <button type="submit" class="modal__submit">送信する</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.chat__edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        const chatId = button.dataset.id;
        const message = button.dataset.message;
        const modal = document.getElementById('editModal');
        const textarea = document.getElementById('editMessage');
        const form = document.getElementById('editForm');
        textarea.value = message;
        form.action = `/chats/${chatId}`;
        modal.style.display = 'block';
    });
});

document.querySelector('.chat__edit-cancel').addEventListener('click', () => {
    document.getElementById('editModal').style.display = 'none';
});

document.addEventListener("DOMContentLoaded", () => {
    const messageInput = document.getElementById("chatMessage");
    const form = document.querySelector(".chat__input-area");
    const storageKey = `chat-message-order-{{ $order->id }}`;
    const savedMessage = localStorage.getItem(storageKey);
    if (savedMessage) messageInput.value = savedMessage;

    const imageBtn = document.getElementById("chatImageBtn");
    const imageInput = document.getElementById("chatImage");

    if (imageBtn && imageInput) {
        imageBtn.addEventListener("click", () => imageInput.click());
        imageInput.addEventListener("change", () => {
            if (imageInput.files.length > 0) {
                alert("選択された画像: " + imageInput.files[0].name);
            }
        });
    }

    messageInput.addEventListener("input", () => {
        localStorage.setItem(storageKey, messageInput.value);
    });

    form.addEventListener("submit", (e) => {
        setTimeout(() => {
            localStorage.removeItem(storageKey);
            messageInput.value = "";
        }, 100);
    });

    document.querySelector("form").addEventListener("submit", () => {
        localStorage.removeItem(storageKey);
    });

    const modal = document.getElementById("completeModal");
    const stars = document.querySelectorAll("#starRating .star");
    const ratingInput = document.getElementById("ratingScore");

    document.getElementById('completeBtn')?.addEventListener('click', e => {
        e.preventDefault();
        if (modal) modal.style.display = 'flex';
    });

    window.addEventListener("click", e => {
        if (modal && e.target === modal) modal.style.display = "none";
    });

    stars.forEach(star => {
        star.addEventListener("mouseenter", () => {
            stars.forEach(s => s.classList.toggle("hovered", s.dataset.value <= star.dataset.value));
        });
        star.addEventListener("mouseleave", () => {
            stars.forEach(s => s.classList.remove("hovered"));
        });
        star.addEventListener("click", () => {
            stars.forEach(s => s.classList.remove("selected"));
            stars.forEach(s => {
                if (s.dataset.value <= star.dataset.value) s.classList.add("selected");
            });
            ratingInput.value = star.dataset.value;
        });
    });

    const sellerStars = document.querySelectorAll("#sellerStarRating .star");
    const sellerInput = document.getElementById("sellerRatingScore");

    sellerStars.forEach(star => {
        star.addEventListener("mouseenter", () => {
            sellerStars.forEach(s => s.classList.toggle("hovered", s.dataset.value <= star.dataset.value));
        });
        star.addEventListener("mouseleave", () => {
            sellerStars.forEach(s => s.classList.remove("hovered"));
        });
        star.addEventListener("click", () => {
            sellerStars.forEach(s => s.classList.remove("selected"));
            sellerStars.forEach(s => {
                if (s.dataset.value <= star.dataset.value) s.classList.add("selected");
            });
            sellerInput.value = star.dataset.value;
        });
    });
});
</script>
@endsection
