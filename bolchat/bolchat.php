<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// session_start();
// $kullaniciAdi = isset($_SESSION['kullaniciAdi']) ? $_SESSION['kullaniciAdi'] : '';
?>

<div id="bolsozluk-chat-container" style="text-align:left;">
    <div id="chat-header">
        <h3>BolChat</h3>
        <div id="online-count">Çevrimiçi: <span>0</span></div>
    </div>
    
    <div id="chat-messages"></div>
    
    <div id="chat-input-area">
        <form id="chat-form" method="post">
            <div class="input-group">
                <input 
                    type="text" 
                    id="nick" 
                    name="nick" 
                    placeholder="<?= isset($kullaniciAdi) && $kullaniciAdi ? 'Giriş yapmış kullanıcı: ' . htmlspecialchars($kullaniciAdi) : 'Nickiniz' ?>" 
                    value="<?= isset($kullaniciAdi) ? htmlspecialchars($kullaniciAdi) : '' ?>" 
                    <?= isset($kullaniciAdi) && $kullaniciAdi ? 'readonly' : '' ?>
                    autocomplete="off"
                >
                <div class="input-main">
                    <textarea 
                        id="message" 
                        name="message" 
                        placeholder="Mesajınızı yazın..." 
                        rows="1" 
                        maxlength="500"
                        autocomplete="off"
                    ></textarea>
                    <button type="submit" id="send-button" aria-label="Mesaj Gönder">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                            <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
#bolsozluk-chat-container {
    font-family: 'Segoe UI', Roboto, sans-serif;
    max-width: 600px;
    margin: 0 auto;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    overflow: hidden;
    background: #fff;
    font-size: 12px;
    line-height: 1.4;
    height: 65vh;
    display: flex;
    flex-direction: column;
}

#chat-header {
    flex: 0 0 auto;
    background: #2c3e50;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: flex-start !important;
    position: relative;
    align-items: center;
}

#chat-header h3 {
    margin: 0;
    font-size: 1.2rem;
}

#online-count {
    font-size: 0.9rem;
    background: rgba(255,255,255,0.2);
    padding: 3px 10px;
    border-radius: 20px;
    margin-left: auto;
}

#chat-messages {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    padding: 15px;
    background: #f5f5f5;
    color: black !important;
}

.message {
    margin-bottom: 15px;
    max-width: 80%;
    animation: fadeIn 0.3s ease;
}

.own-message {
    margin-left: auto;
}

.message-header {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.user-avatar {
    width: 28px;
    height: 28px;
    background: #3498db;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
    font-weight: bold;
    font-size: 0.8rem;
    text-transform: uppercase;
}

.username {
    font-weight: 600;
    font-size: 0.9rem;
    color: #2c3e50;
}

.message-time {
    font-size: 0.7rem;
    color: #95a5a6;
    margin-left: 8px;
    font-family: monospace;
}

.message-content {
    background: white;
    padding: 10px 15px;
    border-radius: 18px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    display: inline-block;
    word-break: break-word;
    white-space: pre-wrap;
}

.own-message .message-content {
    background: #3498db;
    color: white;
    border-bottom-right-radius: 4px;
}

#chat-input-area {
    flex: 0 0 auto;
    background: #fff;
    border-top: 1px solid #eee;
    padding: 15px;
}

.input-group {
    display: flex;
    gap: 10px;
}

.input-main {
    flex: 1;
    display: flex;
    background: #f5f5f5;
    border-radius: 24px;
    padding: 8px 15px;
}

#nick {
    width: 100px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 20px;
    font-size: 0.9rem;
}

textarea#message {
    flex: 1;
    border: none;
    background: transparent;
    resize: none;
    outline: none;
    font-family: inherit;
    font-size: 0.95rem;
    max-height: 120px;
    padding: 5px 0;
}

#send-button {
    background: none;
    border: none;
    color: #3498db;
    cursor: pointer;
    padding: 0 0 0 10px;
    display: flex;
    align-items: center;
}

#send-button:hover {
    color: #2980b9;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 640px) {
    #bolsozluk-chat-container {
        border-radius: 0;
        height: 90vh;
        max-height: 90vh;
    }
    
    #chat-messages {
        min-height: 200px;
    }
    
    .message {
        max-width: 90%;
    }
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    var lastMessageTime = '1970-01-01 00:00:00'; 
    var username = '<?= isset($kullaniciAdi) ? addslashes($kullaniciAdi) : '' ?>';
    var $chatMessages = $('#chat-messages');

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    function loadMessages() {
        $.getJSON('sozluk.php?process=chat&action=get_messages', function(messages) {
            var newMessagesHtml = '';
            var isScrolledToBottom = ($chatMessages.scrollTop() + $chatMessages.innerHeight() >= $chatMessages[0].scrollHeight);

            for (var i = 0; i < messages.length; i++) {
                var msg = messages[i];
                if (msg.created_at > lastMessageTime) {
                    var isOwn = (msg.username === username);
                    newMessagesHtml +=
                        '<div class="message ' + (isOwn ? 'own-message' : '') + '">' +
                            '<div class="message-header">' +
                                '<span class="user-avatar">' + msg.username.charAt(0).toUpperCase() + '</span>' +
                                '<span class="username">' + escapeHtml(msg.username) + '</span>' +
                                '<span class="message-time">' + msg.created_at.substring(11,16) + '</span>' +
                            '</div>' +
                            '<div class="message-content">' + escapeHtml(msg.message) + '</div>' +
                        '</div>';
                    lastMessageTime = msg.created_at;
                }
            }

            if (newMessagesHtml !== '') {
                $chatMessages.append(newMessagesHtml);
                if (isScrolledToBottom) {
                    $chatMessages.scrollTop($chatMessages[0].scrollHeight);
                }
            }
        }).fail(function() {
            console.error('Mesajlar yüklenemedi');
        });

        // Online count güncelle
        $.get('sozluk.php?process=chat&action=get_online_count', function(count) {
            $('#online-count span').text(count);
        }).fail(function() {
            console.error('Çevrimiçi sayısı alınamadı');
        });
    }

    $('#message').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    $('#message').on('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $('#chat-form').submit();
        }
    });

    $('#chat-form').submit(function(e) {
        e.preventDefault();
        var nick = $('#nick').val();
        var message = $('#message').val();

        if (username) {
            nick = username; // Girişli kullanıcıysa backend ile uyumlu olsun diye override
        }

        if (nick.trim() !== '' && message.trim() !== '') {
            $.post('sozluk.php?process=chat', {
                nick: nick,
                message: message,
                action: 'send_message'
            }, function(response) {
                if (response.trim() === 'OK') {
                    $('#message').val('').height('auto');
                    loadMessages();
                } else {
                    alert('Mesaj gönderilemedi: ' + response);
                }
            }).fail(function(jqXHR, textStatus) {
                alert('Hata oluştu: ' + textStatus);
            });
        }
    });

    setInterval(loadMessages, 3000);
    loadMessages();
});
</script>