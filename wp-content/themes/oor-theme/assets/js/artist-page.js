(function() {
  'use strict';

  // Wait for DOM to be ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    // Disable Lenis for artist page to allow native scrolling in tracks container
    disableLenisForArtistPage();
    
    // Description toggle functionality
    initDescription();
    
    // Player functionality
    initPlayer();
    
    // Кастомный скроллбар для контейнера треков
    initArtistTracksScrollbar();
  }

  function disableLenisForArtistPage() {
    // Function to stop Lenis
    function stopLenis() {
      if (window.lenis) {
        try {
          if (window.lenis.stop) window.lenis.stop();
          if (window.lenis.destroy) {
            window.lenis.destroy();
          } else if (window.lenis.off) {
            window.lenis.off('scroll');
          }
          window.lenis = null;
        } catch(e) {
          console.warn('Error disabling Lenis:', e);
        }
      }
    }

    // Stop Lenis immediately
    stopLenis();

    // Also try to stop it after a delay in case it's initialized later
    setTimeout(stopLenis, 100);
    setTimeout(stopLenis, 500);
    setTimeout(stopLenis, 1000);

    // Enable native scrolling
    document.documentElement.style.overflow = 'auto';
    document.body.style.overflow = 'auto';
    document.documentElement.style.overflowY = 'auto';
    document.body.style.overflowY = 'auto';

    // Prevent Lenis from intercepting wheel events on tracks container
    const tracksContainer = document.querySelector('.oor-artist-tracks-container');
    if (tracksContainer) {
      // Remove any event listeners that Lenis might have added
      const newContainer = tracksContainer.cloneNode(true);
      tracksContainer.parentNode.replaceChild(newContainer, tracksContainer);

      // Re-attach our event listeners
      newContainer.addEventListener('wheel', function(e) {
        // Allow native scrolling in tracks container
        const container = this;
        const scrollTop = container.scrollTop;
        const scrollHeight = container.scrollHeight;
        const height = container.clientHeight;
        const isScrollingUp = e.deltaY < 0;
        const isScrollingDown = e.deltaY > 0;
        
        if ((isScrollingUp && scrollTop > 0) || (isScrollingDown && scrollTop < scrollHeight - height)) {
          // Allow native scroll
          return true;
        }
      }, { passive: true });
    }
  }

  function initArtistTracksScrollbar() {
    const container = document.querySelector('.oor-artist-tracks-container');
    const scrollbarEl = document.getElementById('artistTracksScrollbar');
    const thumbEl = document.getElementById('artistTracksScrollbarThumb');
    if (!container || !scrollbarEl || !thumbEl) return;

    let hideTimeout = null;

    function updateThumb() {
      const scrollHeight = container.scrollHeight;
      const clientHeight = container.clientHeight;
      if (scrollHeight <= clientHeight) {
        scrollbarEl.classList.remove('visible');
        return;
      }
      const trackHeight = scrollbarEl.getBoundingClientRect().height;
      const thumbHeight = Math.max(40, (clientHeight / scrollHeight) * trackHeight);
      const scrollTop = container.scrollTop;
      const maxScroll = scrollHeight - clientHeight;
      const thumbTop = maxScroll <= 0 ? 0 : (scrollTop / maxScroll) * (trackHeight - thumbHeight);

      thumbEl.style.height = thumbHeight + 'px';
      thumbEl.style.top = thumbTop + 'px';
      scrollbarEl.classList.add('visible');

      clearTimeout(hideTimeout);
      hideTimeout = setTimeout(function() {
        scrollbarEl.classList.remove('visible');
      }, 1000);
    }

    container.addEventListener('scroll', updateThumb, { passive: true });

    let isDragging = false;
    let dragStartY = 0;
    let dragStartScrollTop = 0;

    thumbEl.addEventListener('mousedown', function(e) {
      e.preventDefault();
      isDragging = true;
      dragStartY = e.clientY;
      dragStartScrollTop = container.scrollTop;
    });

    document.addEventListener('mousemove', function(e) {
      if (!isDragging) return;
      const trackHeight = scrollbarEl.getBoundingClientRect().height;
      const scrollHeight = container.scrollHeight;
      const clientHeight = container.clientHeight;
      const maxScroll = scrollHeight - clientHeight;
      if (maxScroll <= 0) return;
      const deltaY = e.clientY - dragStartY;
      const scrollDelta = (deltaY / trackHeight) * maxScroll;
      container.scrollTop = Math.max(0, Math.min(maxScroll, dragStartScrollTop + scrollDelta));
      dragStartY = e.clientY;
      dragStartScrollTop = container.scrollTop;
      updateThumb();
    });

    document.addEventListener('mouseup', function() {
      isDragging = false;
    });

    document.addEventListener('mouseleave', function() {
      isDragging = false;
    });

    scrollbarEl.querySelector('.oor-artist-tracks-scrollbar-track').addEventListener('click', function(e) {
      if (e.target === thumbEl || thumbEl.contains(e.target)) return;
      const rect = scrollbarEl.getBoundingClientRect();
      const y = e.clientY - rect.top;
      const trackHeight = rect.height;
      const scrollHeight = container.scrollHeight;
      const clientHeight = container.clientHeight;
      const maxScroll = scrollHeight - clientHeight;
      if (maxScroll <= 0) return;
      const ratio = y / trackHeight;
      container.scrollTop = ratio * maxScroll;
      updateThumb();
    });

    var ro = typeof ResizeObserver !== 'undefined' ? new ResizeObserver(updateThumb) : null;
    if (ro) ro.observe(container);
    window.addEventListener('resize', updateThumb);
    updateThumb();
  }

  // Description toggle: полное описание скрыто в HTML (style="display:none"), по клику показываем/скрываем
  function initDescription() {
    var toggleButton = document.getElementById('description-toggle');
    var descriptionContent = document.getElementById('artist-description');
    if (!descriptionContent) return;
    var expandedEls = descriptionContent.querySelectorAll('.oor-artist-description-expanded');
    if (!expandedEls.length) return;

    var isExpanded = false;
    function setExpanded(show) {
      isExpanded = show;
      for (var i = 0; i < expandedEls.length; i++) {
        expandedEls[i].style.display = show ? 'block' : 'none';
      }
      descriptionContent.classList.toggle('expanded', show);
      if (toggleButton) toggleButton.textContent = show ? 'свернуть' : 'подробнее';
    }
    setExpanded(false);

    if (toggleButton) {
      toggleButton.style.cursor = 'pointer';
      toggleButton.addEventListener('click', function() {
        setExpanded(!isExpanded);
      });
    }
  }

  // Audio Player
  function initPlayer() {
    const audio = new Audio();
    audio.preload = 'auto'; // Разрешаем seek до старта play
    let currentTrack = null;
    let isPlaying = false;
    let pendingSeek = null; // Ожидающая перемотка

    // Player elements
    const playerPlayPause = document.getElementById('player-play-pause');
    const playerPrev = document.getElementById('player-prev');
    const playerNext = document.getElementById('player-next');
    const playerTrackName = document.getElementById('player-track-name');
    const playerTime = document.getElementById('player-time');
    const playerProgress = document.getElementById('player-progress');
    const playerHandle = document.getElementById('player-handle');
    const playerProgressBar = document.querySelector('.oor-artist-player-progress-bar') || (playerProgress ? playerProgress.parentElement : null);
    const playerProgressBg = document.getElementById('player-progress-bg');
    const playerVolumeBtn = document.getElementById('player-volume-btn');
    const playerVolumeFill = document.getElementById('player-volume-fill');
    const playerVolumeHandle = document.getElementById('player-volume-handle');
    const playerVolumeBar = playerVolumeFill ? playerVolumeFill.parentElement : null;
    const playerVolumeIcon = playerVolumeBtn ? playerVolumeBtn.querySelector('.oor-artist-player-volume-icon') : null;
    const playerMuteIcon = playerVolumeBtn ? playerVolumeBtn.querySelector('.oor-artist-player-mute-icon') : null;
    let savedVolume = 1;
    let isMuted = false;

    // Track elements
    const tracks = Array.from(document.querySelectorAll('.oor-artist-track'));
    
    if (tracks.length === 0) return;
    
    // Автоматически загружаем первый трек при загрузке страницы (без автовоспроизведения)
    if (tracks.length > 0) {
      const firstTrack = tracks[0];
      const firstTrackSrc = firstTrack.dataset.trackSrc;
      const firstTrackName = firstTrack.querySelector('.oor-artist-track-name')?.textContent || '';
      
      if (firstTrackSrc) {
        // Загружаем первый трек в плеер, но не воспроизводим автоматически
        setTimeout(() => {
          loadAndPlayTrack(firstTrack, firstTrackSrc, firstTrackName, false); // false = не воспроизводить автоматически
        }, 100);
      }
    }

    // Track click handlers
    tracks.forEach(function(track, index) {
      track.addEventListener('click', function(e) {
        // Пропускаем клик, если он был по элементам плеера (прогресс-бар, кнопки и т.д.)
        const player = document.querySelector('.oor-artist-player');
        if (player && player.contains(e.target)) {
          if (window.DEBUG_AUDIO) {
            console.log('Track click: blocked by player element');
          }
          return;
        }
        
        // Пропускаем клик, если он был по прогресс-бару или его элементам
        if (playerProgressBar && (playerProgressBar.contains(e.target) || e.target.closest('.oor-artist-player-progress-bar'))) {
          if (window.DEBUG_AUDIO) {
            console.log('Track click: blocked by progress bar');
          }
          return;
        }
        
        const trackSrc = track.dataset.trackSrc;
        const trackName = track.querySelector('.oor-artist-track-name').textContent;
        
        if (currentTrack === track && isPlaying) {
          // Pause current track
          pauseTrack();
        } else if (currentTrack === track && !isPlaying) {
          // Resume current track
          playTrack();
        } else {
          // Play new track
          loadAndPlayTrack(track, trackSrc, trackName);
        }
      });
    });

    // Player controls
    if (playerPlayPause) {
      playerPlayPause.addEventListener('click', function() {
        if (isPlaying) {
          pauseTrack();
        } else {
          playTrack();
        }
      });
    }

    if (playerPrev) {
      playerPrev.addEventListener('click', function() {
        const currentIndex = tracks.indexOf(currentTrack);
        if (currentIndex > 0) {
          const prevTrack = tracks[currentIndex - 1];
          const trackSrc = prevTrack.dataset.trackSrc;
          const trackName = prevTrack.querySelector('.oor-artist-track-name').textContent;
          loadAndPlayTrack(prevTrack, trackSrc, trackName);
        }
      });
    }

    if (playerNext) {
      playerNext.addEventListener('click', function() {
        const currentIndex = tracks.indexOf(currentTrack);
        if (currentIndex < tracks.length - 1) {
          const nextTrack = tracks[currentIndex + 1];
          const trackSrc = nextTrack.dataset.trackSrc;
          const trackName = nextTrack.querySelector('.oor-artist-track-name').textContent;
          loadAndPlayTrack(nextTrack, trackSrc, trackName);
        }
      });
    }

    // Единая функция перемотки - ждет готовности метаданных
    function seekWhenReady(percentage) {
      if (!audio.src) {
        if (window.DEBUG_AUDIO) {
          console.warn('seekWhenReady: no audio.src');
        }
        return;
      }

      // Проверяем, что длительность числовая и конечная
      const duration = audio.duration;
      // Используем readyState >= 0 (HAVE_NOTHING) - пытаемся установить currentTime в любом случае
      // Браузер сам обработает, если данные еще не готовы
      if (!isNaN(duration) && isFinite(duration) && duration > 0) {
        const seekTime = percentage * duration;
        try {
          audio.currentTime = seekTime;
          pendingSeek = null;
          
          if (window.DEBUG_AUDIO) {
            console.log('Seek applied:', { seekTime, duration, percentage, readyState: audio.readyState, currentTime: audio.currentTime });
          }
        } catch (error) {
          // Если не удалось установить currentTime, сохраняем для применения позже
          pendingSeek = percentage;
          if (window.DEBUG_AUDIO) {
            console.warn('Seek failed, saved as pending:', error);
          }
        }
      } else {
        // Если метаданные еще не готовы, сохраняем процент
        pendingSeek = percentage;
        // Принудительно вызываем загрузку, если она не началась
        if (audio.networkState === 0) {
          audio.load();
        }
        // Логирование для отладки (можно убрать в продакшене)
        if (window.DEBUG_AUDIO) {
          console.log('Seek pending:', {
            duration: duration,
            readyState: audio.readyState,
            networkState: audio.networkState,
            percentage: percentage,
            isNaN: isNaN(duration),
            isFinite: isFinite(duration)
          });
        }
      }
    }

    // Обработчик загрузки метаданных - применяет ожидающую перемотку
    audio.addEventListener('loadedmetadata', function() {
      if (pendingSeek !== null) {
        const duration = audio.duration;
        const savedPendingSeek = pendingSeek; // Сохраняем перед обнулением
        // Проверяем, что duration валидный и конечный
        if (!isNaN(duration) && isFinite(duration) && duration > 0) {
          audio.currentTime = savedPendingSeek * duration;
          pendingSeek = null;
          // Логирование для отладки
          if (window.DEBUG_AUDIO) {
            console.log('Pending seek applied:', {
              duration: duration,
              seekTime: savedPendingSeek * duration,
              readyState: audio.readyState
            });
          }
        } else if (window.DEBUG_AUDIO) {
          console.warn('Invalid duration in loadedmetadata:', {
            duration: duration,
            isNaN: isNaN(duration),
            isFinite: isFinite(duration)
          });
        }
      }
    });

    // Progress bar interaction - полностью переписанная логика
    if (!playerProgressBar) {
      console.warn('Progress bar not found!');
    }
    
    if (playerProgressBar) {
      let dragStartX = 0;
      let dragStartY = 0;
      let lastMouseX = 0;
      let lastMouseY = 0;
      const DRAG_THRESHOLD = 5; // Порог для различения drag от click (5px)
      
      // Функция для вычисления percentage из clientX
      function getPercentageFromClientX(clientX) {
        const rect = playerProgressBar.getBoundingClientRect();
        if (rect.width === 0) {
          if (window.DEBUG_AUDIO) {
            console.warn('Progress bar width is 0!');
          }
          return 0;
        }
        const x = clientX - rect.left;
        const percentage = Math.max(0, Math.min(x / rect.width, 1));
        
        if (window.DEBUG_AUDIO) {
          console.log('getPercentageFromClientX:', { clientX, rectLeft: rect.left, rectWidth: rect.width, x, percentage });
        }
        
        return percentage;
      }

      // Клик по прогресс-бару
      playerProgressBar.addEventListener('click', function(e) {
        // Пропускаем клик по handle
        if (playerHandle && (e.target === playerHandle || playerHandle.contains(e.target))) {
          if (window.DEBUG_AUDIO) {
            console.log('Click on handle, skipping');
          }
          return;
        }
        
        // Проверяем, был ли реальный drag по расстоянию движения
        const deltaX = Math.abs(e.clientX - dragStartX);
        const deltaY = Math.abs(e.clientY - dragStartY);
        const wasRealDrag = (dragStartX !== 0 || dragStartY !== 0) && (deltaX > DRAG_THRESHOLD || deltaY > DRAG_THRESHOLD);
        
        if (wasRealDrag) {
          if (window.DEBUG_AUDIO) {
            console.log('Was real drag, skipping click:', { deltaX, deltaY, dragStartX, dragStartY });
          }
          return;
        }
        
        e.stopPropagation();
        e.preventDefault();
        const percentage = getPercentageFromClientX(e.clientX);
        
        if (window.DEBUG_AUDIO) {
          console.log('Progress bar clicked:', { percentage, clientX: e.clientX, wasRealDrag });
        }
        
        seekWhenReady(percentage);
      }, true); // Используем capture phase для надежности
      
      // Клик по fill (дополнительный обработчик)
      if (playerProgress) {
        playerProgress.addEventListener('click', function(e) {
          // Проверяем, был ли реальный drag
          const deltaX = Math.abs(e.clientX - dragStartX);
          const deltaY = Math.abs(e.clientY - dragStartY);
          const wasRealDrag = (dragStartX !== 0 || dragStartY !== 0) && (deltaX > DRAG_THRESHOLD || deltaY > DRAG_THRESHOLD);
          
          if (wasRealDrag) {
            if (window.DEBUG_AUDIO) {
              console.log('Progress fill: was real drag, skipping');
            }
            return;
          }
          
          e.stopPropagation();
          e.preventDefault();
          const percentage = getPercentageFromClientX(e.clientX);
          
          if (window.DEBUG_AUDIO) {
            console.log('Progress fill clicked:', { percentage, clientX: e.clientX });
          }
          
          seekWhenReady(percentage);
        }, true); // Используем capture phase
      }

      // Drag для handle и прогресс-бара
      playerProgressBar.addEventListener('mousedown', function(e) {
        // Пропускаем клик по handle (он обрабатывается отдельно)
        if (playerHandle && (e.target === playerHandle || playerHandle.contains(e.target))) {
          return;
        }
        
        dragStartX = e.clientX;
        dragStartY = e.clientY;
        lastMouseX = e.clientX;
        lastMouseY = e.clientY;
        
        if (window.DEBUG_AUDIO) {
          console.log('Mouse down on progress bar:', { dragStartX, dragStartY });
        }
      });

      // Drag обработка
      document.addEventListener('mousemove', function(e) {
        if (dragStartX !== 0 && dragStartY !== 0 && audio.src) {
          const deltaX = Math.abs(e.clientX - dragStartX);
          const deltaY = Math.abs(e.clientY - dragStartY);
          
          // Если движение больше порога - это drag
          if (deltaX > DRAG_THRESHOLD || deltaY > DRAG_THRESHOLD) {
            // Добавляем проверку, чтобы не перегружать API
            if (!audio.seeking) {
              const percentage = getPercentageFromClientX(e.clientX);
              seekWhenReady(percentage);
            }
            lastMouseX = e.clientX;
            lastMouseY = e.clientY;
          }
        }
      });

      document.addEventListener('mouseup', function(e) {
        if (dragStartX !== 0 || dragStartY !== 0) {
          if (window.DEBUG_AUDIO) {
            const deltaX = Math.abs(e.clientX - dragStartX);
            const deltaY = Math.abs(e.clientY - dragStartY);
            console.log('Mouse up:', { dragStartX, dragStartY, deltaX, deltaY, wasDrag: deltaX > DRAG_THRESHOLD || deltaY > DRAG_THRESHOLD });
          }
          
          // Сбрасываем флаги с небольшой задержкой, чтобы клик успел обработаться
          setTimeout(function() {
            dragStartX = 0;
            dragStartY = 0;
            lastMouseX = 0;
            lastMouseY = 0;
          }, 100);
        }
      });
    }

    // Volume control
    if (playerVolumeBar) {
      playerVolumeBar.addEventListener('click', function(e) {
        const rect = playerVolumeBar.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const percentage = Math.max(0, Math.min(x / rect.width, 1));
        
        savedVolume = percentage;
        audio.volume = percentage;
        isMuted = false;
        updateVolumeDisplay(percentage);
      });

      // Handle dragging for volume
      let isVolumeDragging = false;
      if (playerVolumeHandle) {
        playerVolumeHandle.addEventListener('mousedown', function(e) {
          isVolumeDragging = true;
          e.preventDefault();
          e.stopPropagation();
        });
      }

      document.addEventListener('mousemove', function(e) {
        if (isVolumeDragging && playerVolumeBar) {
          const rect = playerVolumeBar.getBoundingClientRect();
          const x = Math.max(0, Math.min(e.clientX - rect.left, rect.width));
          const percentage = Math.max(0, Math.min(x / rect.width, 1));
          
          savedVolume = percentage;
          audio.volume = percentage;
          isMuted = false;
          updateVolumeDisplay(percentage);
        }
      });

      document.addEventListener('mouseup', function() {
        isVolumeDragging = false;
      });
    }

    // Mute/unmute functionality
    if (playerVolumeBtn) {
      playerVolumeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (isMuted) {
          // Unmute - restore saved volume
          audio.volume = savedVolume;
          isMuted = false;
          updateVolumeDisplay(savedVolume);
        } else {
          // Mute - save current volume and set to 0
          savedVolume = audio.volume;
          audio.volume = 0;
          isMuted = true;
          updateVolumeDisplay(0);
        }
      });
    }

    function updateVolumeDisplay(percentage) {
      if (playerVolumeFill) {
        playerVolumeFill.style.width = (percentage * 100) + '%';
      }
      if (playerVolumeHandle) {
        playerVolumeHandle.style.left = (percentage * 100) + '%';
      }
      if (playerVolumeIcon && playerMuteIcon) {
        playerVolumeIcon.style.display = isMuted ? 'none' : 'block';
        playerMuteIcon.style.display = isMuted ? 'block' : 'none';
      }
    }

    // Audio event listeners
    audio.addEventListener('timeupdate', function() {
      if (!audio.duration) return;
      
      const percentage = (audio.currentTime / audio.duration) * 100;
      
      if (playerProgress) {
        playerProgress.style.width = percentage + '%';
      }
      if (playerProgressBg) {
        playerProgressBg.style.width = percentage + '%';
      }
      if (playerHandle) {
        playerHandle.style.left = percentage + '%';
      }
      
      if (playerTime) {
        playerTime.textContent = formatTime(audio.currentTime);
      }

      // Update track progress circle
      if (currentTrack) {
        const progressFill = currentTrack.querySelector('.oor-artist-track-progress-fill');
        if (progressFill) {
          const circumference = 2 * Math.PI * 85; // radius = 85
          const offset = circumference - (percentage / 100) * circumference;
          progressFill.style.strokeDashoffset = offset;
        }
      }
    });

    audio.addEventListener('ended', function() {
      // Reset progress on current track
      if (currentTrack) {
        const progressFill = currentTrack.querySelector('.oor-artist-track-progress-fill');
        if (progressFill) {
          const circumference = 2 * Math.PI * 85;
          progressFill.style.strokeDashoffset = circumference;
        }
      }

      // Auto play next track
      const currentIndex = tracks.indexOf(currentTrack);
      if (currentIndex < tracks.length - 1) {
        const nextTrack = tracks[currentIndex + 1];
        const trackSrc = nextTrack.dataset.trackSrc;
        const trackName = nextTrack.querySelector('.oor-artist-track-name').textContent;
        loadAndPlayTrack(nextTrack, trackSrc, trackName);
      } else {
        pauseTrack();
      }
    });

    audio.addEventListener('loadedmetadata', function() {
      if (playerTime) {
        playerTime.textContent = formatTime(0) + ' / ' + formatTime(audio.duration);
      }
    });

    // Helper functions
    function loadAndPlayTrack(track, src, name, autoPlay = true) {
      // Remove playing class from all tracks and reset progress
      tracks.forEach(function(t) {
        t.classList.remove('playing');
        const progressFill = t.querySelector('.oor-artist-track-progress-fill');
        if (progressFill) {
          const circumference = 2 * Math.PI * 85;
          progressFill.style.strokeDashoffset = circumference;
        }
        // Возвращаем иконку плей на всех треках
        const trackPlayIcon = t.querySelector('.oor-artist-track-play-icon');
        if (trackPlayIcon) {
          const paths = (window.OOR_PATHS ? window.OOR_PATHS.assets : '/public/assets');
          trackPlayIcon.src = paths + '/artist-page/play-track.svg';
          trackPlayIcon.style.display = 'block';
        }
      });

      // Set current track
      currentTrack = track;
      currentTrack.classList.add('playing');

      // Update player
      // Важно: сначала останавливаем текущее воспроизведение
      audio.pause();
      
      // Сбрасываем pendingSeek
      pendingSeek = null;
      
      // Устанавливаем новый src
      audio.src = src;
      
      if (playerTrackName) {
        playerTrackName.textContent = name;
      }
      
      // Загружаем аудио для получения метаданных
      audio.load();
      
      // Ждем загрузки метаданных перед воспроизведением
      const metadataHandler = function() {
        // Сбрасываем currentTime только один раз после загрузки метаданных
        audio.currentTime = 0;
        
        if (playerTime && audio.duration) {
          playerTime.textContent = formatTime(0) + ' / ' + formatTime(audio.duration);
        }
        
        // Только после загрузки метаданных начинаем воспроизведение (если autoPlay = true)
        if (autoPlay) {
          audio.play().then(function() {
            isPlaying = true;
            updatePlayPauseButton();
          }).catch(function(error) {
            console.error('Error playing audio:', error);
            isPlaying = false;
            updatePlayPauseButton();
          });
        } else {
          // Если autoPlay = false, только загружаем трек без воспроизведения
          isPlaying = false;
          updatePlayPauseButton();
        }
      };
      audio.addEventListener('loadedmetadata', metadataHandler, { once: true });
    }

    function playTrack() {
      if (!audio.src) {
        // Load first track if no track is loaded
        if (tracks.length > 0) {
          const firstTrack = tracks[0];
          const trackSrc = firstTrack.dataset.trackSrc;
          const trackName = firstTrack.querySelector('.oor-artist-track-name').textContent;
          loadAndPlayTrack(firstTrack, trackSrc, trackName);
        }
        return;
      }

      audio.play().then(function() {
        isPlaying = true;
        updatePlayPauseButton();
      }).catch(function(error) {
        console.error('Error playing audio:', error);
      });
    }

    function pauseTrack() {
      if (audio.src) {
        audio.pause();
      }
      isPlaying = false;
      updatePlayPauseButton();
    }

    function updatePlayPauseButton() {
      if (!playerPlayPause) return;

      const playIcon = playerPlayPause.querySelector('.oor-artist-player-play-icon');
      const pauseIcon = playerPlayPause.querySelector('.oor-artist-player-pause-icon');

      if (playIcon && pauseIcon) {
        // Принудительно обновляем иконки
        if (isPlaying) {
          playIcon.style.display = 'none';
          pauseIcon.style.display = 'block';
        } else {
          playIcon.style.display = 'block';
          pauseIcon.style.display = 'none';
        }
        // Принудительный reflow для гарантированного обновления
        void playerPlayPause.offsetHeight;
      }
      
      // Обновляем иконку на текущем треке
      updateTrackPlayIcon();
    }
    
    function updateTrackPlayIcon() {
      if (!currentTrack) return;
      
      const trackPlayIcon = currentTrack.querySelector('.oor-artist-track-play-icon');
      if (trackPlayIcon) {
        // При воспроизведении показываем иконку паузы, при паузе - иконку плей
        if (isPlaying) {
          const paths = (window.OOR_PATHS ? window.OOR_PATHS.assets : '/public/assets');
          trackPlayIcon.src = paths + '/artist-page/pause-track.svg';
          trackPlayIcon.style.display = 'block';
        } else {
          const paths = (window.OOR_PATHS ? window.OOR_PATHS.assets : '/public/assets');
          trackPlayIcon.src = paths + '/artist-page/play-track.svg';
          trackPlayIcon.style.display = 'block';
        }
      }
    }

    function formatTime(seconds) {
      if (isNaN(seconds)) return '00:00';
      
      const minutes = Math.floor(seconds / 60);
      const secs = Math.floor(seconds % 60);
      
      return (minutes < 10 ? '0' : '') + minutes + ':' + (secs < 10 ? '0' : '') + secs;
    }

    // Set initial volume
    audio.volume = 1;
    savedVolume = 1;
    updateVolumeDisplay(1);
    
    // Экспорт для тестирования (только в dev режиме)
    if (window.DEBUG_AUDIO) {
      window.testAudioSeek = function(percentage) {
        console.log('Test seek to:', percentage);
        seekWhenReady(percentage);
      };
      window.getAudioState = function() {
        return {
          src: audio.src,
          duration: audio.duration,
          currentTime: audio.currentTime,
          readyState: audio.readyState,
          networkState: audio.networkState,
          seeking: audio.seeking,
          pendingSeek: pendingSeek
        };
      };
    }
  }
})();
